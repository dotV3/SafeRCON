# SafeRCON
A PMMP Plugin that allows you to run commands in your server through discord without worrying about RCON insecurities

# Setup
* Add the plugin to your server
* Restart your server
* Edit the config
* Change the `Enabled` to `true` in config then save everything
* Restart the server and you're good to go

# How to use in discord
* Use the command that is set up in the config. (default is `!rcon`)
* Examples of usage:
  - !rcon say hi
  - !rcon ban username

# Config Example
 ```
config:
  IdOnly: false
  RoleOnly: true
  ChannelOnly: true
  Enabled: true 
  Command: "!rcon" 

IdOnly: []

RoleOnly:
  931321481089126401: 931321481089126401
  
ChannelOnly:
  980964684146557009: 980964684146557009
  ```
