----------
INSTALLATION
------------

install docker-compose
```
sudo curl -L https://github.com/docker/compose/releases/download/1.21.2/docker-compose-$(uname -s)-$(uname -m) -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
```

Clone the git repository 
```
git clone git@github.com:digi-trust/cmp-wp-plugin.git
```
Run docker
```
docker-compose build
docker-compose up
```
----------
Settings
------------
The wordpress site can be accessed at: [http://localhost:8181](http://localhost:8181)

The phpmyadmin can be accessed at: [http://localhost:8282](http://localhost:8282) with user:root and password:root

Go to [http://localhost:8181/wp-admin/plugins.php](http://localhost:8181/wp-admin/plugins.php) to activate DigiTrust CMP plugin

Click on DigiTrust CMP activate
![Alt activate DigiTrust CMP](/img/digitrust_admin_plugin.png?raw=true )

Click on DigiTrust menu for configuration
![Alt activate DigiTrust CMP](/img/digiTrust_config.png?raw=true )

Go to [http://localhost:8181](http://localhost:8181) to show CMP modal

![Alt activate DigiTrust CMP](/img/digitrust_cmp_modal.png?raw=true )
