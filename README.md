# Multi Tenant Web Platform

Drupal platform for Multi Tenant Web Platform.

## Local Development

## Development technologies

- [Drupal](https://www.drupal.org)
- [Lando](https://docs.devwithlando.io)
- [Foundation Zurb](https://foundation.zurb.com/sites)
- [Gulp](https://gulpjs.com)
- [Robo](https://robo.li)
 
## Initial Build 

1. Clone VH repository:

          git clone git@github.com:dgcHealth/mtwplatform.git
          
    **NOTE:** In order to be able to push/pull from the VH repository you need to be added as a member. If you don't have access, please let Shadia know.

          
2. Create a `.env` file in the repo root

          touch .env
          
    And then edit them with your preferred text editor (vim/nano/etc...) to add the following contents in it:
    
    ```Bash tab=".env file"
      MYSQL_USER=drupal8
      DRUPAL_MYSQL_HOST=database
      MYSQL_PASSWORD=drupal8
      MYSQL_DATABASE=drupal8
      PHP_IDE_CONFIG="serverName=appserver"
      S3FS_ROOT=yourname #--> Add your actual name here (lowercase no spaces).
      IS_LOCAL=TRUE
    ```

    Please remember to add your actual name for `S3FS_ROOT` var!
    
    :fa-linux: If using linux you can simple do a `cat > .env` paste the file contents using `SHFT+CTRL+V` and then `CTRL+D` to close cat :)

3. Create a `salt.txt` file in the repo root
    ```Bash tab="salt.txt file"
      YF1GMZaTuAjihIOm8GVEM4UR38_KSoHUSSNOhiaTARC_ehBHTdtW-BNKVDkchc2flPaq3MZEsQ
    ```
    
4. Start lando

        lando start

5. Install new site dependecies via composer 

        lando composer install
        
6. Install Drupal (we are using [Robo](https://robo.li/) to run install and config import/export tasks).

        lando robo local:install
        
    When this command ends running, you should have a brand new D8 site with all the configs imported :fa-magic:. 
    
    You can login as Drupal administrator (ie: uid=1) using **admin/admin** User/Password combination.
    
    In case you are curious you can check `dgc-jau-demo/RoboFile.php` to take a look at those defined tasks.
        
7. And finally, enable our custom Default Content module in order to... create some dummy content (mainly groups, and group content nodes).

        lando drush en dgc_default_content
        
 Enabling this module will create not only content, but also some users (one for each Role). You can generate a One Time Login link using Drush, ie: `lando drush uli tenant` 
 
 
## Updating your site

### Install new site dependecies via composer 
```sh
lando composer install
``` 

### Import config changes. 
```sh
lando robo local:update
```