<?php
function getAuthInfo(){
    return \Auth::user();
}
function isNotAdmin(){
    $user = getAuthInfo();
    if ($user->role == 'Admin'){
        return false;
    }
    return true;
}
