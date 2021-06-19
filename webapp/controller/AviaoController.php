<?php
use ArmoredCore\Controllers\BaseController;
use ArmoredCore\WebObjects\Redirect;
use ArmoredCore\WebObjects\Session;
use ArmoredCore\WebObjects\View;
use Carbon\Carbon;
use ArmoredCore\WebObjects\Debug;
use ArmoredCore\WebObjects\Post;

/**
 * Created by PhpStorm.
 * User: smendes
 * Date: 09-05-2016
 * Time: 11:30
 */
class AviaoController extends BaseController
{

    public function index(){

        $aeroportos = Aeroporto::all();
        return View::make('passagemvenda.index',['aeroportos'=> $aeroportos]);
    }


    public function ListarAvioes(){

        $avioes= Avioe::all();


        return View::make('aviao.gestaoAvioes',['avioes'=>$avioes]);
    }

    public function edit($id)
    {
        $aviao=Avioe::find([$id]);

        if(is_null($aviao))
        {
            //
        }
        else
        {
            return View::make('aviao.edit', ['aviao' => $aviao]);
        }
    }

    public function update($id)
    {
        //find resource (activerecord/model) instance where PK = $id
        //your form name fields must match the ones of the table fields
        $aviao = Avioe::find([$id]);
        $aviao->update_attributes(Post::getAll());

        if($aviao->is_valid()){
        $aviao->save();
        Redirect::ToRoute('aviao/ListarAvioes');
        } else {
            //Redirect::flashToRoute('aeroporto/edit', ['aeroporto' => $aeroporto]);
        }
    }

    public function delete($id)
    {
        $aviao = Avioe::find([$id]);
        $aviao->delete();
        Redirect::toRoute('aviao/ListarAvioes');
    }

    public function worksheet(){

        View::attachSubView('titlecontainer', 'layout.pagetitle', ['title' => 'MVC Worksheet']);

        return View::make('home.worksheet');
    }

    public function setsession(){
        $dataObject = MetaArmCoreModel::getComponents();
        Session::set('object', $dataObject);

        Redirect::toRoute('home/worksheet');
    }

    public function showsession(){
        $res = Session::get('object');
        var_dump($res);
    }

    public function destroysession(){

        Session::destroy();
        Redirect::toRoute('home/worksheet');
    }


}