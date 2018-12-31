<?php

namespace tungsten3;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\{Command,CommandSender, CommandExecutor, ConsoleCommandSender};

use pocketmine\Player;
use pocketmine\event\player\PlayerChatEvent as Chat;

class Main extends PluginBase implements Listener{
    public $database;

    private $task;

    public $server = "Server 2";

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $host = "localhost";
        $user = "root";
        $password = "";
        $db = "TungDB";

        $this->database = @new \mysqli($host, $user, $password, $db);
        if ($this->database->connect_error) {
            $this->getLogger()->info("Kết nối thất bại: " . $this->database->connect_error);
        }

        $db_create = "CREATE DATABASE IF NOT EXISTS TungDB";
        if ($this->database->query($db_create) === TRUE) {
        } else {
            $this->getLogger()->info("Tạo database thất bại: " . $this->database->error);
        }


        $table1 = "CREATE TABLE IF NOT EXISTS Tung_tb5 (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         name TEXT ,
         message TEXT
        )";
        $table2 = "CREATE TABLE IF NOT EXISTS Tung_tb6 (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         name TEXT ,
         message TEXT
        )";
        $table3 = "CREATE TABLE IF NOT EXISTS Tung_tb7 (
         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         name TEXT ,
         message TEXT
        )";
        if ($this->database->query($table1) === TRUE and $this->database->query($table2) ===TRUE and $this->database->query($table3)=== True) {
        } else {
            $this->getLogger()->info("Tạo table thất bại: " . $this->database->error);
        }

    }

    public function PlayerChatMessage(Chat $e){
     $p = $e->getPlayer();
     $n = $p->getName();
     $msg = $e->getMessage();
     $insert1 = "INSERT INTO Tung_tb5 (name,message) VALUES ('$n','$msg')";
     $insert2 = "INSERT INTO Tung_tb6 (name,message) VALUES ('$n','$msg')";
     $insert3 = "INSERT INTO Tung_tb7 (name,message) VALUES ('$n','$msg')";

     if ($this->database->query($insert2) === TRUE and $this->database->query($insert3) === TRUE) {
            $last_id = $this->database->insert_id;
    //        $this->getLogger()->info("Add record succesfull with ID: $last_id");
     }else {
         $this->getLogger()->info("Error: " . $this->database->error);
     }
    }

    public function ShowChat(){
        $sec = "SELECT * FROM Tung_tb5";
        $result = $this->database->query($sec);
        if (!$result) {
            $this->getLogger()->info($this->database->error);
        }
        foreach ($this->getServer()->getOnlinePlayers() as $player){
            $name = $player->getName();
            while ($row = $result->fetch_assoc()) {
                //   $this->getLogger()->info($row["id"]." - ".$row["name"]." - ".$row["message"]);
                $name = $player->getName();
                if ($name !== $row["name"]) {
                    $this->getServer()->broadcastMessage("[".$this->server."] ".$row["name"]." -> ".$row["message"]);
                    $del = "DELETE FROM Tung_tb5 WHERE id";
                    $this->database->query($del);
                }
        }
        }
    }
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
	  switch(strtolower($cmd->getName())){
          case "chaton":
	          $this->task = new PopupTask($this);
	          $this->getScheduler()->scheduleRepeatingTask($this->task,5);
	          break;
          case "chatoff":
              $this->getScheduler()->cancelTask($this->task->getTaskId());
              break;
	  }

	 return true;
	}
   
}