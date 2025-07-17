<?php

namespace Test\Model;

class User
{
    private $db;
    private $table = 'user';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findByUsername($username)
    {
        $query = $this->db->buildSelect()
            ->cols(['id', 'username', 'password', 'failed', 'blocked', 'lastlogin'])
            ->from($this->table)
            ->where('username', $username);

        $result = $this->db->fetchAssoc($query);
        return $result ? $result[0] : null;
    }

    public function authenticate($username, $password)
    {
        $user = $this->findByUsername($username);
        if (!$user) {
            return ['status' => 'invalid', 'user' => null];
        }

        if ($user['blocked'] === '1') {
            return ['status' => 'blocked', 'user' => $user];
        }

        // ⚠ Klartext-Vergleich, da kein Hashing in der CSV
        if ($password === $user['password']) {
            $this->resetFailedLogin($user['id']);
            $this->updateLastLogin($user['id']);
            return ['status' => 'success', 'user' => $user];
        } else {
            $this->incrementFailedLogin($user);
            return ['status' => 'invalid', 'user' => $user];
        }
    }

    public function incrementFailedLogin($user)
    {
        $failed = (int)$user['failed'] + 1;
        $blocked = $failed >= 3 ? '1' : '0';

        $updateQuery = $this->db->buildUpdate()
            ->table($this->table)
            ->set('failed', $failed)
            ->set('blocked', $blocked)
            ->where('id', $user['id']);

        $this->db->execute($updateQuery);
    }

    public function resetFailedLogin($userId)
    {
        $updateQuery = $this->db->buildUpdate()
            ->table($this->table)
            ->set('failed', 0)
            ->set('blocked', '0')
            ->where('id', $userId);

        $this->db->execute($updateQuery);
    }

    public function updateLastLogin($userId)
    {
        $updateQuery = $this->db->buildUpdate()
            ->table($this->table)
            ->set('lastlogin', date('Y-m-d H:i:s'))
            ->where('id', $userId);

        $this->db->execute($updateQuery);
    }

    public function getUserById($userId)
    {
        $query = $this->db->buildSelect()
            ->cols(['id', 'username', 'lastlogin'])
            ->from($this->table)
            ->where('id', $userId);

        $result = $this->db->fetchAssoc($query);
        return $result ? $result[0] : null;
    }
}
