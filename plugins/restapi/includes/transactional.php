<?php

namespace phpListRestapi;

use http\Env\Response;

defined('PHPLISTINIT') || die;

/**
 * Class Transactional
 * Manage phplist transactional mails.
 */
class Transactional
{
    /**
     * Send a new transactional mail
     *
     * <p><strong>Parameters:</strong><br/>
     * [*subject] {string} <br/>
     * [*recipient] {string} <br/>
     * [*category] {string} <br/>
     * [*message] {string} <br/>
     * [*textmessage] {string} <br/>
     * [*template] {string} <br/>
     * <p><strong>Returns:</strong><br/>
     * Success message
     * </p>
     */
    public static function transactionalSend()
    {
        try {
            $subject = $_REQUEST['subject'];
            $recipient = $_REQUEST['recipient'];
            $category = $_REQUEST['category'];
            $message = $_REQUEST['message'];
            $textMessage = $_REQUEST['textmessage'];
            $template = $_REQUEST['template'];
            $attributes = $_REQUEST['attributes'];

            if (empty($subject) || empty($recipient) || empty($category) || empty($message) || empty($textMessage) || empty($template)) {
                Response::outputErrorMessage('missing parameter');
            }

            if (!validateEmail($recipient)) {
                Response::outputErrorMessage('invalid email address');
            }
            $_REQUEST['transactional'] = 1;

            // add new list
            $_REQUEST['name'] = "Transactional: $subject";
            $_REQUEST['description'] = "$subject: $recipient";
            $_REQUEST['category'] = $category;
            $_REQUEST['listorder'] = 0;
            $_REQUEST['active'] = 1;
            $list = Lists::listAdd();
            error_log("List added with id $list->id");

            // add subscriber
            $subscriber = Subscribers::subscriberGetByEmail($recipient);
            if($subscriber->id) {
                // add subscriber to list
                Lists::listSubscriberAdd($list->id,$subscriber->id);
            } else {
                $_REQUEST['email'] = $recipient;
                $_REQUEST['lists'] = $list->id;
                $subscriber = Subscribers::subscribe();
                // active/confirm new user
                $_REQUEST['id'] = $subscriber->id;
                $_REQUEST['htmlemail'] = 1;
                $_REQUEST['confirmed'] = 1;
                Subscribers::subscriberUpdate();
            }

            // save attributes if any
            if (!empty($attributes)) {
                Subscribers::subscriberUpdateAttributes( $subscriber->id);
            }

            // add campaign
            $_REQUEST['fromfield'] = 'itasportaldevelopment@itas-gmbh.de VBL';
            $_REQUEST['replyto'] = 'itasportaldevelopment@itas-gmbh.de';
            $_REQUEST['footer'] = '';
            $_REQUEST['status'] = 'submitted';
            $_REQUEST['sendformat'] = 'HTML';
            $_REQUEST['embargo'] = date('Y-m-d H:i:s');
            $_REQUEST['rsstemplate'] = null;
            $_REQUEST['owner'] = '1';
            $_REQUEST['htmlformatted'] = '1';
            $campaign = Campaigns::campaignAdd();
            error_log("Campaign added with id $campaign->id");

            // connect new campaign with new list
            Lists::listCampaignAdd($list->id, $campaign->id);
            error_log("Campaign with id $campaign->id connected to list with id $list->id");

        } catch (\Exception $e) {
            Response::outputError($e);
        }
        $_REQUEST['transactional'] = 0;
        Response::outputMessage('success');
    }
}
