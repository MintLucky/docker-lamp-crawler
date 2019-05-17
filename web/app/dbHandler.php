<?php

class dbHandler
{
    private $host = '10.128.0.4';
    private $db   = 'users_db';
    private $user = 'root';
    private $pass = 'qwerty12345ytrewq';
    private $charset = 'utf8mb4';
    private $PDO = null;

    public function __construct()
    {
        $this->getConnection();
    }

    private function getConnection()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];

        $this->PDO = new PDO($dsn, $this->user, $this->pass, $opt);
    }

    public function insertUsersToDB($country, $users, $log_file)
    {
        $insertedUsersCount = 0;
        foreach($users as $user) {
            $langString = '';
            $languages = $user['languages'];
            foreach($languages as $category => $l) {
                $langString .= $category . '[ ';
                foreach($l as $lang) {
                    $langString .= 'code:' . $lang['code'] . ', ' . 'name:' . $lang['name'] . '; ';
                }
                $langString .= '] ';
            }
            $sql = "INSERT INTO europe_users (country, userId, avatarUrl, isVerified, publicName, status, lastLogin, responseTimeText, responseRateText, totalReferencesCount, profileLink, friendsCount, languages, aboutText) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt= $this->PDO->prepare($sql);

            try {
                $stmt->execute(
                    [
                        $country,
                        $user['id'],
                        $user['avatarUrl'],
                        (int)$user['isVerified'],
                        $user['publicName'],
                        $user['status'],
                        $user['lastLogin'],
                        $user['responseTimeText'],
                        $user['responseRateText'],
                        $user['totalReferencesCount'],
                        $user['profileLink'],
                        $user['friendsCount'],
                        $langString,
                        $user['aboutText']
                    ]
                );
            }
            catch(PDOException $ex) {
                fwrite($log_file,  time() ." Error on inserting = " . $ex .  PHP_EOL);
                fwrite($log_file,  time() ." Error userId = " . $user['id'] . " Error user name = " . $user['publicName'] .  PHP_EOL);
            }
            $insertedUsersCount++;
        }

        fwrite($log_file,  time() ." Inserted " . $insertedUsersCount . " users for " . $country .  PHP_EOL);
    }

    public function getCountUsersForCountry($country_name)
    {
        $sql = "SELECT COUNT(*) FROM europe_users WHERE country = '{$country_name}';";
        $stmt= $this->PDO->prepare($sql);
        $stmt->execute();
        $number_of_rows = $stmt->fetchColumn();
        return $number_of_rows;
    }

    public function getCountUsers()
    {
        $sql = "SELECT COUNT(*) FROM europe_users;";
        $stmt= $this->PDO->prepare($sql);
        $stmt->execute();
        $number_of_rows = $stmt->fetchColumn();
        return $number_of_rows;
    }
}
