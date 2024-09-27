# Amazon AWS S3 CLI PHP Class

## Introduction

Amazon AWS S3 CLI PHP Class is a simple php class for interacting with Amazon S3 object storage based server. This class is utilizing the **aws cli** and not using the common **AWS PHP SDK**, therefore, this class is compatible with any version of PHP. 

## Requirement

This class requires:
* AWS Cli

To install the AWS CLI, follow these instructions:

```
wget "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -O "awscliv2.zip"
# or using curl: curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install
```

## Usage

### Class Inheritance

```
require_once('phps3awscli.php');
$phps3 = new Phps3awscli($access_key_id, $secret_access_key, $endpoint_url);
```

### Retrieving Buckets

```
$output = $phps3->list_buckets();
print_r($output);
```

Possible output:

```
Array
(
    [0] => test
    [1] => test1
    [2] => test2
)
```

### Checking Bucket exists

```
$bucket = 'test';
$output = $phps3->check_bucket($bucket);
echo $output ? "Bucket '$bucket' exists!" : "Bucket '$bucket' does not exist!";
```

Possible output:
```
Bucket 'test' exists!
```


### Uploading a single file

Format: `push($bucket, $local_path, $remote_path_inside_bucket, $acl)` , $acl is optional and depend on the storage vendor

Upon succeed, the function will return the remote file path on format s3://[bucket]/filepath.ext. When failed, empty string is returned.

```
$output = $phps3->push('test', '/var/www/html/aws/images/simple.jpeg', 'images/simple.jpg');
echo $output;
```

Possible output:
```
s3://test/images/simple.jpeg
```

### Downloading a single file

Format: `pull($bucket, $remote_path_inside_bucket, $local_path, $acl)`

Upon succeed, the function will return local file path relative to the current working directory. When failed, empty string is returned.

```
$output = $phps3->pull('test', 'images/simple.jpg', '/var/www/html/aws/images/simple.jpeg');
echo $output;
```

Possible output:
```
images/simple.jpeg
```

### Uploading a directory and all the subdirectories from local to bucket
Format: `push_all($bucket, $local_dir_path, $remote_dir_path_inside_bucket, $acl)`

Upon succeed, the function will return an array of all the uploaded remote file paths. When failed, empty array is returned.

```
$output = $phps3->push_all('test', '/var/www/html/aws/', 'aws/');
print_r($output);
```

Possible output:
```
Array
(
    [0] => s3://test/aws/README.md
    [1] => s3://test/aws/phps3awscli.php
    [2] => s3://test/aws/example.php
    [3] => s3://test/aws/LICENSE
    [4] => s3://test/aws/images/simple.jpeg
)
```

### Downloading a directory and all the subdirectories from bucket to local
Format: `pull_all($bucket, $remote_dir_path_inside_bucket, $local_dir_path, $acl)`

Upon succeed, the function will return an array of all the downloaded local file paths. When failed, empty array is returned.

```
$output = $phps3->pull_all('test', 'aws/', '/var/www/html/aws/');
print_r($output);
```

Possible output:
```
Array
(
    [0] => ./README.md
    [1] => ./example.php
    [2] => ./phps3awscli.php
    [3] => ./LICENSE
    [4] => images/simple.jpeg
)
```