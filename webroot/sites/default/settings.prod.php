<?php

// @codingStandardsIgnoreFile

/**
 * @file
 * PROD override configuration feature.
 */

/**
 * S3FS configuration for AWS S3 or Minio (file storage)
 */
$settings['s3fs.use_s3_for_public'] = TRUE;
$settings['s3fs.use_s3_for_private'] = TRUE;
$settings['s3fs.access_key'] = getenv('S3FS_ACCESS_KEY');
$settings['s3fs.secret_key'] = getenv('S3FS_SECRET_KEY');
$config['s3fs.settings']['bucket'] = getenv('S3FS_BUCKETNAME');
$config['s3fs.settings']['hostname'] = getenv('S3FS_HOSTNAME');
$config['s3fs.settings']['region'] = getenv('S3FS_REGION');
$config['s3fs.settings']['use_instance_profile'] = (bool) getenv('S3FS_USE_INSTANCE_PROFILE');
$config['s3fs.settings']['use_customhost'] = (bool) getenv('S3FS_USE_CUSTOM_HOST');
$config['s3fs.settings']['use_https'] = (bool) getenv('S3FS_USE_HTTPS');
$config['s3fs.settings']['use_path_style_endpoint'] = (bool) getenv('S3FS_USE_PATH_STYLE_ENDPOINT');
$config['s3fs.settings']['public_folder'] = 'public';
$config['s3fs.settings']['private_folder'] = 'private';
$config['s3fs.settings']['root_folder'] = getenv('S3FS_ROOT');
$config['s3fs.settings']['no_rewrite_cssjs'] = TRUE;
$config['s3fs.settings']['set_public_read_acl'] = (bool) getenv('S3FS_SET_PUBLIC_READ_ACL');
if (getenv('S3FS_DOMAIN')) {
  $config['s3fs.settings']['use_cname'] = TRUE;
  $config['s3fs.settings']['domain'] = getenv('S3FS_DOMAIN');
}

/**
 * SMTP.
 */
$config['mailsystem.settings']['defaults']['sender'] = "SMTPMailSystem";
$config['smtp.settings']['smtp_on'] = "on";
$config['smtp.settings']['smtp_host'] = getenv('SMTP_HOST');
$config['smtp.settings']['smtp_port'] = getenv('SMTP_PORT');
$config['smtp.settings']['smtp_protocol'] = getenv('SMTP_PROTOCOL');
$config['smtp.settings']['smtp_username'] = getenv('SMTP_USERNAME');
$config['smtp.settings']['smtp_password'] = getenv('SMTP_PASSWORD');
$config['smtp.settings']['smtp_from'] = getenv('SMTP_FROM');
$config['smtp.settings']['smtp_fromname'] = getenv('SMTP_FROMNAME');
