#!/bin/bash
cd /tmp
curl -O https://bootstrap.pypa.io/get-pip.py
sudo python get-pip.py
sudo pip install awscli

aws opsworks register --infrastructure-class ec2 --region us-east-1 --stack-id 172f3886-2bac-4379-a29a-604c1e2480b1 --local