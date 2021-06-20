<?php
use ArmoredCore\Controllers\BaseController;
use ArmoredCore\WebObjects\Redirect;
use ArmoredCore\WebObjects\Session;
use ArmoredCore\WebObjects\View;
use Carbon\Carbon;
use ArmoredCore\WebObjects\Debug;
use ArmoredCore\WebObjects\Post;

ActiveRecord\Connection::$datetime_format = 'Y-m-d H:i:s';

/**
 * Created by PhpStorm.
 * User: smendes
 * Date: 09-05-2016
 * Time: 11:30
 */
class EscalaController extends BaseController
{

    public function ListarEscalas(){

        $escalas=Escala::all();

            foreach($escalas as $escala)
            {
                $origem=Aeroporto::find_by_sql('select nome_aeroporto from `aeroportos` where id_aeroporto=?',array($escala->origem));
                $destino=Aeroporto::find_by_sql('select nome_aeroporto from aeroportos where id_aeroporto=?',array($escala->destino));
            }

        return View::make('escala.gestaoEscalas',['escalas'=>$escalas,'origem'=>$origem,'destino'=>$destino]);
    }

    public function create()
    {
        $aeroportos=Aeroporto::all();
        $aviao=Avioe::all();
        return View::make('escala.new',['aeroportos'=>$aeroportos,'avioes'=>$aviao]);
    }

    public function store()
    {
        //create new resource (activerecord/model) instance with data from POST
        //your form name fields must match the ones of the table fields



        $escala = new Escala();
        $escala->distancia= Post::get('distancia');
        $escala->data= Post::get('data');
        $escala->origem= Post::get('origem');
        $escala->destino= Post::get('destino');



            $query=Escala::find_by_origem_and_destino($escala->origem,$escala->destino);
            //var_dump($query);
            if($query==null){
                $escala->save();
                Redirect::toRoute('escala/gestaoEscalas');
            }
            else{
                echo "Já existe";
            }

        $escalaInserida=Escala::last();
        $aviaoEscala= new AvioesEscala();
        $query=AvioesEscala::find_by_id_escala_and_id_aviao($escalaInserida->id_escala,Post::get('id_aviao'));
        //var_dump($query);
        if($query==null) {
            $aviaoEscala->id_escala = $escalaInserida->id_escala;
            $aviaoEscala->id_aviao = Post::get('id_aviao');
            $aviaoEscala->save();
            $aviaoEscalaInserido=AvioesEscala::last();
            var_dump($aviaoEscalaInserido);
            $escalaInserida->update_attribute('id_aviaoescala',$aviaoEscalaInserido->id_aviaoescala);
            $escalaInserida->save();
            Redirect::toRoute('escala/gestaoEscalas');
        }
        else{
            echo "Já existe";

        }
    }



    public function edit($id){
        $escala = Escala::find([$id]);

        if (is_null($escala)) {
            //TODO redirect to standard error page
        } else {
            return View::make('escala.edit', ['escala' => $escala]);
        }
    }

    public function update($id)
    {
        //find resource (activerecord/model) instance where PK = $id
        //your form name fields must match the ones of the table fields
        $escala = Escala::find([$id]);
        $escala->update_attributes(Post::getAll());

        //if($aeroporto->is_valid()){
        $escala->save();
        Redirect::toRoute('escala/ListarEscalas');
        /*} else {
            //redirect to form with data and errors
            Redirect::flashToRoute('aeroporto/edit', ['aeroporto' => $aeroporto]);
        }*/
    }

    public function destroy($id)
    {
        $escala = Escala::find([$id]);
        $escala->delete();
        Redirect::toRoute('escala/ListarEscalas');
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