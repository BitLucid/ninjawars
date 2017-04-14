#!/bin/bash
sudo touch /var/log/nginx/ninjawars.chat-server.log
sudo chmod g+rw /var/log/nginx/ninjawars.chat-server.log
sudo nohup php bin/chat-server.php > /var/log/nginx/ninjawars.chat-server.log 2>&1 &
