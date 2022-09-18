<?php
declare(strict_types=1);

namespace RealYXNG\SafeRCON;



/*  
 *  
 *  _____            ___     ____   ___   _  _____ 
 *  |  __ \          | \ \   / /\ \ / / \ | |/ ____|
 *  | |__) |___  __ _| |\ \_/ /  \ V /|  \| | |  __ 
 *  |  _  // _ \/ _` | | \   /    > < | . ` | | |_ |
 *  | | \ \  __/ (_| | |  | |    / . \| |\  | |__| |
 *  |_|  \_\___|\__,_|_|  |_|   /_/ \_\_| \_|\_____|
 *    
 *     
 */


use JaxkDev\DiscordBot\Models\Messages\Message;
use JaxkDev\DiscordBot\Plugin\Events\MessageSent;
use JaxkDev\DiscordBot\Plugin\Storage;
use JaxkDev\DiscordBot\Plugin\Main as DiscordBot;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;

class RCON extends PluginBase implements Listener
{

    public int $Rolecount = 0;
    public int $Channelcount = 0;
    private $discord;


    public function onEnable () : void
    {
        
        $this->saveDefaultConfig();
        $this->discord = $this->getServer()->getPluginManager()->getPlugin("DiscordBot");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        
    }
    public function Msg (MessageSent $event) {

        // == Member == //

        $member = Storage::getMember($event->getMessage()->getAuthorId());
        // ======= //

        // == User == //

        $user_id = (($member?->getUserId()) ?? (explode(".", $event->getMessage()->getAuthorId() ?? "na.na")[1]));
        $user = Storage::getUser($user_id);

        // ======= //

        // == Extra Variables == //

        $cid = $event->getMessage()->getChannelId();
        $mention = "<@".$user_id.">";
        $content = trim($event->getMessage()->getContent());
        $args = explode( " " , $event->getMessage()->getContent() );

        // ======= //

        // == Crash Preventing == //
        if ($user === null){
            //Shouldn't happen but it's just to prevent any crashes that might possibly happen
            return;
        }
        if (strlen($content) === 0){
            //Happens in-case the message is simply a picture or a video or embed but no text
            return;
        }
        // ======= //


        // == Getting Configs == //

        if($this->getConfig()->getNested("config") !== null){
            $IdOnly = $this->getConfig()->getNested("config.IdOnly");
            $RoleOnly = $this->getConfig()->getNested("config.RoleOnly");
            $enabled = $this->getConfig()->getNested("config.Enabled");
            $ChannelOnly = $this->getConfig()->getNested("config.ChannelOnly");
            $Command = $this->getConfig()->getNested("config.Command");
        }else{
            return;
        }

        if(!$enabled) return;

        // ======= //


        if (isset( $args[0] )) {
            if ($args[0] == $Command) {
                // == Security Checks == //
                if($IdOnly){
                    if($this->getConfig()->getNested("IdOnly") == null){
                        $this->getLogger()->warning("IdOnly is ENABLED but there's nothing stored in the config");
                        return;
                    }
                    if($this->getConfig()->getNested("IdOnly.$user_id") == null) return;
                }
                if($RoleOnly){
                    if($this->getConfig()->getNested("RoleOnly") == null){
                        $this->getLogger()->warning("RoleOnly is ENABLED but there's nothing stored in the config");
                        return;
                    }

                    foreach ($this->getConfig()->getNested("RoleOnly") as $roleID){
                        if((str_contains(implode(" ", $member->getRoles()), "$roleID"))){
                            $this->Rolecount = $this->Rolecount + 1;
                        }
                    }
                    if($this->Rolecount == 0){
                        return;
                    }else{
                        $this->Rolecount = 0;
                    }

                }
                if($ChannelOnly){
                    if($this->getConfig()->getNested("ChannelOnly") == null){
                        $this->getLogger()->warning("ChannelOnly is ENABLED but there's nothing stored in the config");
                        return;
                    }

                    foreach ($this->getConfig()->getNested("ChannelOnly") as $channelID){
                        if(str_contains("$channelID", "$cid")){
                            $this->Channelcount = $this->Channelcount + 1;
                        }
                    }
                    if($this->Channelcount == 0){
                        return;
                    }else{
                        $this->Channelcount = 0;
                    }
                }

                // ======= //


                // == Final Code that runs the command == //

                $code = str_replace("$Command ", "", $event->getMessage()->getContent());
                Server::getInstance()->dispatchCommand(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), "$code");
                $msg = new Message($cid, null, "Successfully ran the command \n $code \n By: $mention.");
                $this->getDiscord()->getApi()->sendMessage($msg);

                // ======= //

            }
        }
    }



    public function getDiscord(): DiscordBot
    {
        return $this->discord;
    }


}
