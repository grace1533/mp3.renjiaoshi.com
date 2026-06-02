<?php
/**
 * 音乐管理系统
 *
 * @version   2.0
 * @author    战神~~巴蒂 [jyuucn@163.com]
 * @license   http://jyuu.cn [未经授权严禁私自出售，否者承担法律责任]
 * @copyright 2014 - 2017
 */
 
return [
    /** 模板语言 */
    'music' => 'Music',
    'top_music' => 'MusicTOP',
    'latest_music' => 'LatestMusic',
    'hot_listen' => 'HotListen',
    'hot_down' => 'HotDownload',
    'recommend_music' => 'BestSingle',
    
    'album' => 'album',
    'latest_album' => 'LatestAlbum',
    'recommend_album' => 'BestAlbum',
    
    'artist' => 'artist',
    'latest_artist' => 'LatestArtist',
    'recommend_artist' => 'BestArtist',
    
    'genre' => 'category',
    'recommend_genre' => 'BestCategory',
    
    'ranks' => 'Ranks',
    'recommend_ranks' => 'BestRank',
    
    'tags' => 'Tags',
    'recommend_tags' => 'BestTags',
    
    'user' => 'user',
    'share_user' => 'BestUser',
    
    'friend_link' => 'FriendLink',
    'more' => 'more',
    
    /**信息提示 **/
    'login_success' => 'Login was successful ',
    'signup_success' => 'registered successfully',
    'pwd_rest_success' => 'Password reset success',
    'signup_off'  => 'Sorry, the current site user registration has been closed',
    'active_email_success'  => 'The system has sent an activation email. Please go to your mailbox and activate it within 24 hours',
    'email_vcode_success' => 'The verification code is successful, please go to your mailbox!',
    'active_error' => 'The activation address is incorrect or expired!',
    'active_success' => 'Users activate successfully!',
    'logout_success' => 'Logout successfully',
    
    /** 上传文件 */
    'file_upload_success' => 'file upload success',
    'audio_upload_success' => 'Audio file upload success',
    'image_upload_success' => 'Images file upload success',
    'avatar_upload_success' => 'Avatar upload success',
    
    //400*** api 参数验证错误返回的信息
    /*返回状态*/
    404 => 'not found', //404 not found
    401 => 'Unauthorized', //Unauthorized
    403 => 'Forbidden', //Forbidden
    500 => 'Internal Server Error', //Internal Server Error
    
    
    //已下是 api 对应语言
    40004 => 'parameter error',
    
    //操作限制错误码
    40011 => 'The collection has been capped',
    40012 => 'The number of concerns has been capped',
    40013 => 'The number of heads this month has been capped',
    40014 => 'Data revisions have been capped this month',
    40015 => 'The number of password changes has been capped this month',
    40016 => 'You can\'t focus on yourself',
    
    //文件错误码
    40020 => 'PHP File upload extension does not open',
    40021 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
    40022 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
    40023 => 'The uploaded file was only partially uploaded',
    40024 => 'No file was uploaded',
    40026 => 'Missing a temporary folder',
    40027 => 'Failed to write file to disk',
    40028 => 'A PHP extension stopped the file upload',
    40029 => 'File uploading option in disabled in php.ini',
    
    400201 => 'The uploaded file exceeds the post_max_size directive in php.ini',
    400202 => 'The uploaded file Maximum number exceeds the max_file_uploads directive in php.ini',
    400203 => 'Cannot find uploaded file(s) identified by field',
    400204 => 'No file was choosed. Please select one.',
    400206 => 'extensions not allowed',
    400207 => 'FileType not allowed',
    400208 => 'File is too big',
    400209 => 'Maximum number of files exceeded',
    400210 => 'Only part of the file has been uploaded！',
    400211 => 'Failed to write file to disk.',
    400212 => 'Directory to create failure',
    400213 => 'The uploaded file Illegal.',
    400214 => 'The image file Illegal.',
    400215 => "File could't be download.",
    400216 => "File already exists!",
    
    //验证错误
    40030 => 'Parameter validation error',
    40031 => 'User name error',
    40032 => 'Email format error',
    40033 => 'wrong password',
    40034 => 'Phone number error',
    40035 => 'Verification code error',
    40036 => 'The verification code has expired',
    40037 => 'The token is invalid',
    40038 => 'repetition logging in',
    
    //账号相关错误
    40040 => 'The user does not exist.',
    40041 => 'Account activation',
    40044 => 'The account has been disabled',
    40045 => 'The mail does not exist, please confirm whether the input is correct',
    
    //服务状态错误码
    40401 => 'Sorry you haven\'t logged in yet, please log in',
    40403 => 'Unauthorized access',
    40404 => 'The type of operation does not exist',
    40405 => 'The operation is too frequent',
    40406 => 'Can\'t operate in a short time',
    40407 => 'No data for the time being',
    
    //服务内存程序出错
    40500 => 'Sorry for the failure, please try again later',
    40501 => 'User registration failed, please try again later',
    40502 => 'User login failed, please try again later',
    40503 => 'Activation of email failed, please try again later',
    40504 => 'The verification code failed, please try again later',
    40505 => 'Exit login failed, please try again later',
    40506 => 'File upload failed, please try again later',
];