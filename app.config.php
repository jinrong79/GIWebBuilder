<?php
\CONFIG::$APP=array_merge(\CONFIG::$APP,array(

        'siteName'=>'GI WebBuilder',//website name viewed.

        /*url setting of current application*/
        'urlHome'=>'/index.html',
        'urlAdmin'=>'/admin/index.html',

        /* current site domain */
        'siteDomain'=>'www.gibuilder.com',


        /*db connection setting
          - two settings: local testing setting and site lunching setting.
          - default db connection setting can choose one of settings automatically by HTTP_HOST var.
        */
        //db connection setting for local testing:
        'dbConnectSettingLocal'=> array(
            'driver'=>'mysqli',
            'host' => 'p:localhost:3306',
            'user' => 'root',
            'pwd' => 'sheepyang',
            'dbname' => 'db_eyb'
        ),
        //db connection setting for remote site:
        'dbConnectSettingSite'=> array(
            'driver'=>'mysqli',
            'host' => 'p:localhost:3306',
            'user' => 'root',
            'pwd' => 'oArpCnd4',
            'dbname' => 'db_eyb'
        ),

        //authorization map: will be filled in global.auth.php
        'authMap'=>array(),
        //model interface setting: will be filled in global.mi.php
        'MIsetting'=>array(),

    )
);


