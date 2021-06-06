<?php


use ArmoredCore\WebObjects\View;

class BackOfficeController
{

    public function login()
    {
        return View::make('backoffice.login');
    }


    // VERIFICAR SE ESTÁ CORRETO
    public function processlogin()
    {
        $user= new User(Post::GetAll());

        $userisset= User::find_by_name_and_password($user->username, $user->password);

        if(is_null($userisset)){
            //Error Page
        }
        else{
            $_SESSION['login']= $user->username;
            $_SESSION['perfil']= $userisset->id_perfil;

            return View::make('backoffice.home');
        }
    }

    public function home()
    {
        return VIew::make('backoffice.home');
    }

}