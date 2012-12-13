<!DOCTYPE html>
<html>
<head>
    <title>Twitter Share</title>
</head>
<body>
<?php
// The share parameters
$twitterParameters = array(
    'url'  => 'The url to share',
    'via'  => 'user name this is via',
    'text' => 'text of the tweet'
);

$twitterLink = 'https://twitter.com/share?' . http_build_query($twitterParameters);
?>
<a href="<?php echo $twitterLink ?>">Click this link to go share on twitter</a>
</body>
</html>