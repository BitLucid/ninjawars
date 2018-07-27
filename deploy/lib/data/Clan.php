<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\Message;
use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Player;
use \PDO;

/**
 * who/what/why/where
 *
 * Ninja clans with their various members
 */
class Clan {
    public $id;
    private $name;
    private $avatarUrl;
    private $description;
    private $founder;

    public function __construct($p_id=null, $p_name=null, $data=null) {
        $this->setID($p_id);

        if (!$p_name) {
            $p_name = $this->nameFromId($p_id);
        }

        $this->setName($p_name);

        if ($data) {
            $this->setAvatarUrl($data['clan_avatar_url']);
            $this->setDescription($data['description']);
            $this->setFounder($data['clan_founder']);
        }
    }

    public function getName(): string {
        return $this->name;
    }

    public function setID($p_id) {
        $this->id = (int)$p_id;
    }

    public function setName(string $p_name) {
        $this->name = trim($p_name);
    }

    /**
     * @return int
     */
    public function getLeaderID(): int {
        $leaderInfo = $this->getLeaderInfo();
        return $leaderInfo['player_id'];
    }

    /**
     * @return string
     */
    public function getFounder() {
        if (!$this->founder) {
            $this->founder = query_item('select clan_founder from clan where clan_id = :id', [':id'=>$this->id]);
        }

        return $this->founder;
    }

    /**
     * @return void
     */
    public function setDescription(string $desc) {
        $this->description = (string) $desc;
    }

    /**
     * @return void
     */
    public function getDescription() {
        return $this->description;
    }

    public function setFounder(string $founder) {
        $this->founder = $founder;
    }

    /**
     * @return string
     */
    public function getAvatarUrl(): string {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(string $url) {
        $this->avatarUrl = $url;
    }

    // End of getters and setters.

    private function nameFromId(int $id) {
        return query_item(
            'SELECT clan_name FROM clan WHERE clan_id = :id',
            [ ':id' => [$id, PDO::PARAM_INT]]
        );
    }

    /**
     * Return only the single clan leader and their information.
     *
     * @return array
     */
    public function getLeaderInfo(): array {
        return $this->getAllClanLeaders()->fetch();
    }

    /**
     * Get the current clan leaders.
     *
     */
    public function getAllClanLeaders(): \PDOStatement {
        DatabaseConnection::getInstance();
        $leaders = DatabaseConnection::$pdo->prepare("SELECT clan_id, clan_name, clan_founder, player_id, uname
            FROM clan JOIN clan_player ON clan_id = _clan_id JOIN players ON player_id = _player_id
            WHERE active = 1 AND member_level > 0 AND clan_id = :clan ORDER BY member_level DESC, level DESC");
        $leaders->bindValue(':clan', $this->id);
        $leaders->execute();

        return $leaders;
    }

    /**
     * @return boolean|String
     */
    public function addMember(Player $ninja, Player $adder) {
        if ($this->hasMember($ninja->id())) {
            return 'That ninja is already a member of the clan.';
        }

        // Not an insert_query because there is no sequence involved or needed.
        query('insert into clan_player (_clan_id, _player_id) values (:c, :p)', [':c'=>$this->id, ':p'=>$ninja->id()]);
        query('update players set verification_number = :new_num where player_id = :id', [':new_num'=>rand(1, 999999), ':id'=>$ninja->id()]);

        Message::create([
            'send_from' => $adder->id(),
            'send_to'   => $ninja->id(),
            'message'   => "CLAN: You have been accepted into ".$this->getName(),
            'type'      => 0,
        ]);

        return true;
    }

    /**
     * Passively invite a character to a clan with a message and link.
     *
     * @return string|null
     */
    public function invite(Player $p_target, Player $p_inviter) {
        if (!$p_target || empty($p_target)) {
            return 'No such ninja.';
        }

        $active = $p_target->isActive();

        if (!$active) {
            $failureError = 'That ninja is not active.';
        } else {
            $inviteMsg = $p_inviter->name().' has invited you into their clan, '.$this->getName().'. '
                .'To accept, choose their clan '.$this->getName().' on the '
                .'[href:/clan/view?clan_id='.$this->id.'|clan joining page].';

            Message::create([
                'send_from' => $p_inviter->id(),
                'send_to'   => $p_target->id(),
                'message'   => $inviteMsg,
                'type'      => 0,
            ]);

            $failureError = null;
        }

        return $failureError;
    }

    /**
     * For when a player chooses to leave their clan of their own volition.
     */
    public function leave(Player $ninja) {
        $this->kickMember($ninja->id(), $ninja, true);
    }

    /**
     * When a leader removes a member without choice.
     */
    public function kickMember(int $playerId, Player $kicker, $selfLeave=false) {
        $today = date("F j, Y, g:i a");

        query(
            "DELETE FROM clan_player WHERE _player_id = :player AND _clan_id = :clan",
            [
                ':player' => $playerId,
                ':clan'   => $this->id,
            ]
        );

        if ($selfLeave) {
            $msg = "You have been kicked out of ".$this->getName()." by ".$kicker->name()." on $today.";
        } else {
            $msg = "You have left clan ".$this->getName()." on $today.";
        }

        Message::create([
            'send_from' => $kicker->id(),
            'send_to'   => $playerId,
            'message'   => $msg,
            'type'      => 0,
        ]);

        return true;
    }

    /**
     * Check if a character is a member of this clan
     */
    public function hasMember(int $playerId): bool {
        $query = 'SELECT _player_id FROM clan_player WHERE _player_id = :pid AND _clan_id = :clan_id';
        $args  = [
            ':pid'     => $playerId,
            ':clan_id' => $this->id,
        ];

        return (bool) query_item($query, $args);
    }

    /**
     * @return array(int, int, ...)
     */
    public function getMemberIds(): array {
        $playerRows = query_array(
            'SELECT player_id FROM players LEFT JOIN clan_player ON _player_id = player_id WHERE _clan_id = :cid',
            [':cid' => $this->id]
        );

        $ids = array();

        foreach ($playerRows as $row) {
            $ids[] = $row['player_id'];
        }

        return $ids;
    }

    /**
     */
    public function getMemberCount(): int {
        return (int) query_item(
            'SELECT count(*) FROM clan_player JOIN players ON player_id = _player_id WHERE _clan_id = :clan',
            [':clan' => [$this->id, \PDO::PARAM_INT]]
        );
    }
    /**
     * Delete a clan after sending a message to all clan members.
     * @return void
     */
    public function disband() {
        DatabaseConnection::getInstance();
        $leader = $this->getLeaderID();

        $message = "Your leader has disbanded your clan. You are alone again.";

        $statement = DatabaseConnection::$pdo->prepare("SELECT _player_id FROM clan_player WHERE _clan_id = :clan");
        $statement->bindValue(':clan', $this->id);
        $statement->execute();

        while ($data = $statement->fetch()) {
            $memberId = $data[0];

            Message::create([
                'send_from' => $leader,
                'send_to'   => $memberId,
                'message'   => $message,
                'type'      => 0,
            ]);
            // Create both an event and a message!
            Event::create(0, $memberId, $message);
        }

        // Deletion of the clan_player connections should cascade from the deletion of the clan, at least ideally.
        $statement = DatabaseConnection::$pdo->prepare("DELETE FROM clan WHERE clan_id = :clan");
        $statement->bindValue(':clan', $this->id);
        $statement->execute();
    }

    public function promoteMember(int $ninja_id): bool {
        $query = 'UPDATE clan_player SET member_level = (member_level + 1) WHERE _player_id = :pid';
        $args = [
            ':pid' => $ninja_id,
        ];

        return (bool) update_query($query, $args);
    }

    /**
     * Get the members of a clan,
     */
    public function getMembers(): array {
        $membersArray = query_array(
            'SELECT uname, accounts.active_email as email, clan_name, level, days, clan_founder, player_id, member_level '.
            'FROM clan JOIN clan_player ON _clan_id = :clan_id AND clan_id = _clan_id JOIN players ON player_id = clan_player._player_id '.
            'JOIN account_players on player_id = account_players._player_id join accounts on account_id = _account_id '.
            'AND active = 1 ORDER BY level, health DESC',
            [':clan_id' => $this->id]
        );

        $max = query_item(
            'SELECT max(level) AS max '.
            'FROM clan '.
            'JOIN clan_player ON _clan_id = :clan_id AND clan_id = _clan_id '.
            'JOIN players ON player_id = _player_id AND active = 1',
            [':clan_id'=>$this->id]
        );

        // Modify the members by reference
        foreach ($membersArray as &$member) {
            $member['leader'] = false;
            $member['size']   = floor( ( ($member['level'] - $member['days'] < 1 ? 0 : $member['level'] - $member['days']) / $max) * 2) + 1;

            // Calc the member display size based on their level relative to the max.
            if ($member['member_level'] >= 1) {
                $member['leader'] = true;
                $member['size']   = max($member['size'] + 2, 3);
            }

            $member['gravatar_url'] = Player::find($member['player_id'])->avatarUrl();
        }

        return $membersArray;
    }

    /**
     * checks that an avatar url is valid
     *
     * @param string $dirty_url
     * @return boolean
     */
    public static function clanAvatarIsValid(string $dirty_url): bool {
        if ($dirty_url === '' || $dirty_url === null) {
            return true;  // Allows for no clan avatar.
        }

        $is_url = ($dirty_url == filter_var($dirty_url, FILTER_VALIDATE_URL));

        if (!$is_url) {
            return false;
        } else {
            // TODO: Allow ninjawars as a host, and imgur.com as a host as well.
            $parts = @parse_url($dirty_url);
            return !!preg_match('#[\w\d]*\.imageshack\.[\w\d]*#i', $parts['host']);
        }
    }

    /**
     * Save the url of the clan avatar to the database.
     *
     * @param String $url
     * @param int $clan_id
     * @return void
     */
    public static function saveClanAvatarUrl(string $url, int $clan_id) {
        $update = 'UPDATE clan SET clan_avatar_url = :url WHERE clan_id = :clan_id';
        query($update, array(':url'=>$url, ':clan_id'=>$clan_id));
    }

    /**
     * Save the clan description to the database.
     *
     * @return string $desc
     * @return int $clan_id
     * @return void
     */
    public static function saveClanDescription(string $desc, int $clan_id) {
        $update = 'UPDATE clan SET description = :desc WHERE clan_id = :clan_id';
        query($update, array(':desc'=>$desc, ':clan_id'=>$clan_id));
    }

    /**
     *
     * @param Player $p_leader
     * @param String $p_clan_name
     * @return Clan
     */
    public static function create(Player $p_leader, string $p_clan_name) {
        DatabaseConnection::getInstance();

        $clan_name = trim($p_clan_name);

        $result = DatabaseConnection::$pdo->query("SELECT nextval('clan_clan_id_seq')");
        $clan_id = $result->fetchColumn();

        $statement = DatabaseConnection::$pdo->prepare('INSERT INTO clan (clan_id, clan_name, clan_founder) VALUES (:clanID, :clanName, :leader)');
        $statement->bindValue(':clanID', $clan_id);
        $statement->bindValue(':clanName', $clan_name);
        $statement->bindValue(':leader', $p_leader->name());
        $statement->execute();

        $statement = DatabaseConnection::$pdo->prepare('INSERT INTO clan_player (_player_id, _clan_id, member_level) VALUES (:leader, :clanID, 2)');
        $statement->bindValue(':clanID', $clan_id);
        $statement->bindValue(':leader', $p_leader->id());
        $statement->execute();

        return new Clan($clan_id, $clan_name);
    }

    /**
     * Rename a clan
     *
     * @param int $p_clanID
     * @param String $p_newName
     * @return String
     * @note
     * Does not check for validity, simply renames the clan to the new name.
     */
    public static function renameClan(int $p_clanID, string $p_newName) {
        DatabaseConnection::getInstance();

        $statement = DatabaseConnection::$pdo->prepare('UPDATE clan SET clan_name = :name WHERE clan_id = :clan');
        $statement->bindValue(':name', $p_newName);
        $statement->bindValue(':clan', $p_clanID);
        $statement->execute();

        return $p_newName;
    }

    /**
     * Unique clan name check, ignores whitespace
     *
     * @param String $p_potential
     * @return boolean
     */
    public static function isUniqueClanName(string $p_potential) {
        return !(bool)query_row(
            "SELECT clan_name FROM clan WHERE regexp_replace(clan_name, '[[:space:]]', '', 'g') ~~* regexp_replace(:testName, '[[:space:]]', '', 'g')",
            [':testName' => $p_potential]
        );
    }

    /**
     * Validates a clan name
     *
     * @param String $potential
     * @return int
     * @note
     * Clan name requirements:
     * Must be at least 3 characters to a max of 24, can only contain:
     * letters, numbers, non-consecutive spaces, underscores, or dashes.
     * Must begin and end with non-whitespace characters.
     */
    public static function isValidClanName(string $potential) {
        $potential = (string)$potential;
        return preg_match("#^[\da-z_\-]([\da-z_\-]| [\da-z_\-]){2,25}$#i", $potential);
    }

    /**
     * Find a clan by identity
     *
     * @param int|string $identity
     * @return Clan|null
     */
    public static function find($identity) {
        $clan_info = null;
        if(is_numeric($identity)){
            $clan_info = query_row(
                'select clan_id, clan_name, clan_created_date, clan_founder, clan_avatar_url, description from clan where clan_id = :id',
                [':id'=>[$identity, PDO::PARAM_INT]]
            );
        } elseif(static::isValidClanName($identity)) {
            $clan_info = query_row(
                'select clan_id, clan_name, clan_created_date, clan_founder, clan_avatar_url, description from clan where clan_name = :name',
                [':name'=>$identity]
            );
        }

        if (empty($clan_info)) {
            return null;
        } else {
            return new Clan($clan_info['clan_id'], $clan_info['clan_name'], $clan_info);
        }
    }

    /**
     * Find the clan belonging to a player, if any
     *
     * @param Player $player
     * @return Clan|null
     */
    public static function findByMember(Player $player) {
        $clan_info = query_row('select clan_id, clan_name, clan_created_date, clan_founder, clan_avatar_url, description from clan JOIN clan_player ON clan_id = _clan_id where _player_id = :pid', [':pid'=>$player->id()]);

        if (empty($clan_info)) {
            return null;
        } else {
            return new Clan($clan_info['clan_id'], $clan_info['clan_name'], $clan_info);
        }
    }

    /**
     * Write the clan to the database
     */
    public function save() {
        if (!$this->id) {
            throw new \RuntimeException('Clan cannot be saved as it does not yet have an id.');
        }

        $updated = update_query(
            'update clan set clan_name = :name, clan_founder = :founder, clan_avatar_url = :avatar_url, description = :desc where clan_id = :id',
            [
                ':name'       => $this->getName(),
                ':founder'    => $this->getFounder(),
                ':avatar_url' => $this->getAvatarUrl(),
                ':desc'       => $this->getDescription(),
                ':id'         => $this->id,
            ]
        );

        return (bool)$updated;
    }

    /**
     * Determines the criteria for how clans get ranked and tagged
     *
     * @return array
     * @note
     * returns only non-empty clans.
     */
    public static function rankings(): array {
        $res = [];

        // sum the levels of the players (minus days of inactivity) for each clan
        $counts = query('SELECT sum(round(((level+4)/5+8)-least((days/3), 50))) AS sum, sum(active) as member_count, clan_name, clan_id
            FROM clan JOIN clan_player ON clan_id = _clan_id JOIN players ON _player_id = player_id
            WHERE active = 1 GROUP BY clan_id, clan_name ORDER BY sum DESC');

        foreach ($counts as $clan_info) {
            $max = (isset($max) ? $max : $clan_info['sum']);
            // *** make percentage of highest, multiply by 10 and round to give a 1-10 size ***
            $res[$clan_info['clan_id']]['name']  = $clan_info['clan_name'];
            $res[$clan_info['clan_id']]['score'] = floor(( (($clan_info['sum'] - 1 < 1 ? 0 : $clan_info['sum'] - 1)) / $max) * 10) + 1;
        }

        return $res;
    }
}
