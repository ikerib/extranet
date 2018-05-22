
#Symfony 4 + Bootstrap + Font Awesome

LDAP Auth

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
