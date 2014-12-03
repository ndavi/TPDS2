<?php

namespace Weblinks\DAO;

use Weblinks\Domain\Link;

class LinkDAO extends DAO {

    /**
     * @var \Weblinks\DAO\UserDAO
     */
    protected $userDAO;

    public function setUserDAO($userDAO) {
        $this->userDAO = $userDAO;
    }

    /**
     * Returns a list of all links, sorted by id.
     *
     * @return array A list of all links.
     */
    public function findAll() {
        $sql = "select * from t_link order by lin_id desc";
        $result = $this->getDb()->fetchAll($sql);

        // Convert query result to an array of domain objects
        $entities = array();
        foreach ($result as $row) {
            $id = $row['lin_id'];
            $entities[$id] = $this->buildDomainObject($row);
        }
        return $entities;
    }

    /**
     * Returns a link matching the supplied id.
     *
     * @param integer $id
     *
     * @return \Weblinks\Domain\Link|throws an exception if no matching user is found
     */
    public function find($id) {
        $sql = "select * from t_link where lin_id=?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));
        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No link matching id " . $id);
    }

    /**
     * Saves a link into the database.
     *
     * @param \Weblinks\Domain\Link $link The link to save
     */
    public function save($link) {
        $linkData = array(
            'lin_title' => $link->getTitle(),
            'lin_url' => $link->getUrl(),
            'usr_id' => $link->getUser()->getId()
        );
        if ($link->getId()) {
            // The link has already been saved : update it
            $this->getDb()->update('t_link', $linkData, array('lin_id' => $link->getId()));
        } else {
            // The link has never been saved : insert it
            $this->getDb()->insert('t_link', $linkData);
            // Get the id of the newly created link and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $link->setId($id);
        }
    }

    /**
     * Removes all links for a user
     *
     * @param $userId The id of the user
     */
    public function deleteAllByUser($userId) {
        $this->getDb()->delete('t_link', array('usr_id' => $userId));
    }
    
    /**
     * Removes the link from the database.
     *
     * @param $id int The id of the link to remove
     */
    public function delete($id) {
        // Delete the user
        $this->getDb()->delete('t_link', array('lin_id' => $id));
    }

    /**
     * Creates an Link object based on a DB row.
     *
     * @param array $row The DB row containing Link data.
     * @return \Weblinks\Domain\Link
     */
    protected function buildDomainObject($row) {
        $link = new Link();
        $link->setId($row['lin_id']);
        $link->setUrl($row['lin_url']);
        $link->setTitle($row['lin_title']);

        if (array_key_exists('usr_id', $row)) {
            // Find and set the associated author
            $userId = $row['usr_id'];
            $user = $this->userDAO->find($userId);
            $link->setUser($user);
        }

        return $link;
    }

}
