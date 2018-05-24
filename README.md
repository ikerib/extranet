
#Extranet


### Requirements
 - apache2
 - php
    - php7.2
    - php7.2-ldap
    - php7.2-zip
 - mysql
 - node
 - yarn

 - a2enmod ldap
 - a2enmod rewrite

##### Elastic Search
```
sudo apt update
wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-6.2.4.deb
sudo dpkg -i elasticsearch-6.2.4.deb
sudo systemctl enable elasticsearch.service
   
sudo bin/elasticsearch-plugin install ingest-attachment

```

Egin beharrekoak:

```php
cd <<project dir>>
mkdir EXTRANET
sudo setfacl -R -m u:"www-data":rwX -m u:`whoami`:rwX EXTRANET
sudo setfacl -dR -m u:"www-data":rwX -m u:`whoami`:rwX EXTRANET
```
 
Oharrak:

public/export karpetak idazteko baimenak behar ditu                                                                                          
