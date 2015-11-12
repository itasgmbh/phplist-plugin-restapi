<?php

namespace phpListRestapi;

defined('PHPLISTINIT') || die;

/**
 * Class Messages
 * Manage phplist Messages.
 */
class Messages
{
    public static function messageGet($id = 0)
    {
        if ($id == 0) {
            $id = $_REQUEST['id'];
        }
        
        $params = array(
            'id' => array($id,PDO::PARAM_INT),
            );
        
        Common::select('Message', 'SELECT * FROM '.$GLOBALS['table_prefix'].'message WHERE id=:id;',$params, true);
    }
    
    public static function messagesCount()
    {
        Common::select('Messages', 'SELECT count(id) as total FROM '.$GLOBALS['table_prefix'].'message',array(),true);
    }


    /**
     * Get all the Campaigns in the system.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [order_by] {string} name of column to sort, default "id".<br/>
     * [order] {string} sort order asc or desc, default: asc.<br/>
     * [limit] {integer} limit the result, default 10 (max 10)<br/>
     * [offset] {integer} offset of the result, default 0.<br/>
     * </p>
     * <p><strong>Returns:</strong><br/>
     * List of Campaigns.
     * </p>
     */
    public static function messagesGet($order_by = 'modified', $order = 'desc', $limit = 10, $offset = 0)
    {
        if (isset($_REQUEST['order_by']) && !empty($_REQUEST['order_by'])) {
            $order_by = $_REQUEST['order_by'];
        }
        if (isset($_REQUEST['order']) && !empty($_REQUEST['order'])) {
            $order = $_REQUEST['order'];
        }
        if (isset($_REQUEST['limit']) && !empty($_REQUEST['limit'])) {
            $limit = sprintf('%d',$_REQUEST['limit']);
        }
        if (isset($_REQUEST['offset']) && !empty($_REQUEST['offset'])) {
            $offset = sprintf('%d',$_REQUEST['offset']);
        }
        if ($limit > 10) {
            $limit = 10;
        }
        
        $params = array (
            'order_by' => array($order_by,PDO::PARAM_STR),
            'order' => array($order,PDO::PARAM_STR),
            'limit' => array($limit,PDO::PARAM_INT),
            'offset' => array($offset,PDO::PARAM_INT),
        );
        Common::select('Messages', 'SELECT * FROM '.$GLOBALS['table_prefix'].'message ORDER BY :order_by :order LIMIT :limit OFFSET :offset;',$params);
    }

    /**
     * Add a new campaign.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [*subject] {string} <br/>
     * [*fromfield] {string} <br/>
     * [*replyto] {string} <br/>
     * [*message] {string} <br/>
     * [*textmessage] {string} <br/>
     * [*footer] {string} <br/>
     * [*status] {string} <br/>
     * [*sendformat] {string} <br/>
     * [*template] {string} <br/>
     * [*embargo] {string} <br/>
     * [*rsstemplate] {string} <br/>
     * [*owner] {string} <br/>
     * [htmlformatted] {string} <br/>
     * <p><strong>Returns:</strong><br/>
     * The message added.
     * </p>
     */
    public static function messageAdd()
    {
        $sql = 'INSERT INTO '.$GLOBALS['table_prefix'].'message (subject, fromfield, replyto, message, textmessage, footer, entered, status, sendformat, template, embargo, rsstemplate, owner, htmlformatted ) VALUES ( :subject, :fromfield, :replyto, :message, :textmessage, :footer, now(), :status, :sendformat, :template, :embargo, :rsstemplate, :owner, :htmlformatted );';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('subject', $_REQUEST['subject'], PDO::PARAM_STR);
            $stmt->bindParam('fromfield', $_REQUEST['fromfield'], PDO::PARAM_STR);
            $stmt->bindParam('replyto', $_REQUEST['replyto'], PDO::PARAM_STR);
            $stmt->bindParam('message', $_REQUEST['message'], PDO::PARAM_STR);
            $stmt->bindParam('textmessage', $_REQUEST['textmessage'], PDO::PARAM_STR);
            $stmt->bindParam('footer', $_REQUEST['footer'], PDO::PARAM_STR);
            $stmt->bindParam('status', $_REQUEST['status'], PDO::PARAM_STR);
            $stmt->bindParam('sendformat', $_REQUEST['sendformat'], PDO::PARAM_STR);
            $stmt->bindParam('template', $_REQUEST['template'], PDO::PARAM_INT);
            $stmt->bindParam('embargo', $_REQUEST['embargo'], PDO::PARAM_STR);
            $stmt->bindParam('rsstemplate', $_REQUEST['rsstemplate'], PDO::PARAM_STR);
            $stmt->bindParam('owner', $_REQUEST['owner'], PDO::PARAM_INT);
            $stmt->bindParam('htmlformatted', $_REQUEST['htmlformatted'], PDO::PARAM_STR);
            $stmt->execute();
            $id = $db->lastInsertId();
            $db = null;
            self::messageGet($id);
        } catch (PDOException $e) {
            Response::outputError($e);
        }
    }

    /**
     * Update existing message/campaign.
     * 
     * <p><strong>Parameters:</strong><br/>
     * [*id] {integer} <br/>
     * [*subject] {string} <br/>
     * [*fromfield] {string} <br/>
     * [*replyto] {string} <br/>
     * [*message] {string} <br/>
     * [*textmessage] {string} <br/>
     * [*footer] {string} <br/>
     * [*status] {string} <br/>
     * [*sendformat] {string} <br/>
     * [*template] {string} <br/>
     * [*embargo] {string} <br/>
     * [*rsstemplate] {string} <br/>
     * [owner] {string} <br/>
     * [htmlformatted] {string} <br/>
     * <p><strong>Returns:</strong><br/>
     * The message added.
     * </p>
     */
    public static function messageUpdate($id = 0)
    {
        if ($id == 0) {
            $id = $_REQUEST['id'];
        }
        $sql = 'UPDATE '.$GLOBALS['table_prefix'].'message SET subject=:subject, fromfield=:fromfield, replyto=:replyto, message=:message, textmessage=:textmessage, footer=:footer, status=:status, sendformat=:sendformat, template=:template, sendstart=:sendstart, rsstemplate=:rsstemplate, owner=:owner, htmlformatted=:htmlformatted WHERE id=:id;';
        try {
            $db = PDO::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam('id', $id, PDO::PARAM_INT);
            $stmt->bindParam('subject', $_REQUEST['subject'], PDO::PARAM_STR);
            $stmt->bindParam('fromfield', $_REQUEST['fromfield'], PDO::PARAM_STR);
            $stmt->bindParam('replyto', $_REQUEST['replyto'], PDO::PARAM_STR);
            $stmt->bindParam('message', $_REQUEST['message'], PDO::PARAM_STR);
            $stmt->bindParam('textmessage', $_REQUEST['textmessage'], PDO::PARAM_STR);
            $stmt->bindParam('footer', $_REQUEST['footer'], PDO::PARAM_STR);
            $stmt->bindParam('status', $_REQUEST['status']);
            $stmt->bindParam('sendformat', $_REQUEST['sendformat'], PDO::PARAM_STR);
            $stmt->bindParam('template', $_REQUEST['template'], PDO::PARAM_INT);
            $stmt->bindParam('embargo', $_REQUEST['embargo'], PDO::PARAM_STR);
            $stmt->bindParam('rsstemplate', $_REQUEST['rsstemplate'], PDO::PARAM_STR);
            $stmt->bindParam('owner', $_REQUEST['owner'], PDO::PARAM_INT);
            $stmt->bindParam('htmlformatted', $_REQUEST['htmlformatted'], PDO::PARAM_BOOL);
            $stmt->execute();
            $db = null;
            self::messageGet($id);
        } catch (PDOException $e) {
            Response::outputError($e);
        }
    }

    public static function formtokenGet()
    {
        $key = md5(time().mt_rand(0, 10000));
        Sql_Query(sprintf('insert into %s (adminid,value,entered,expires) values(%d,"%s",%d,date_add(now(),interval 1 hour))',
        $GLOBALS['tables']['admintoken'], $_SESSION['logindetails']['id'], $key, time()), 1);
        $response = new Response();
        $response->setData('formtoken', $key);
        $response->output();
    }
}
