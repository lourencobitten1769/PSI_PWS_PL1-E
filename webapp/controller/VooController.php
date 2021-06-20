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
class VooController extends BaseController
{

    public function index(){

        $aeroportos = Aeroporto::all();
        return View::make('passagemvenda.index',['aeroportos'=> $aeroportos]);
    }

    public function create()
    {
        return View::make('voo.new');
    }

    public function store()
    {
        //create new resource (activerecord/model) instance with data from POST
        //your form name fields must match the ones of the table fields
        $voo = new Voo(Post::getAll());

        if($voo->is_valid()){
            $voo->save();
            Redirect::toRoute('voo/ListarVoos');
        } else {
            //redirect to form with data and errors
            Redirect::flashToRoute('voo/create', ['voo' => $voo]);
        }
    }



    public function MostrarVoosAssociados(){
        echo $_SESSION['id'];



        $join = 'LEFT JOIN escalas e ON(voo.id_escala = e.id_escala)';
        $voos = Voo::all(array('joins' => $join));
        //var_dump($passagens);
        return View::make('passagemvenda.comprarPassagem',['voos'=>$voos]);
    }

    public function ListarVoos(){

        $voos=Voo::all();

        foreach($voos as $voo){

            $escalas=Escala::find_all_by_id_escala($voo->id_escala);

            foreach($escalas as $escala)
            {
                $origem=Aeroporto::find_by_sql('select nome_aeroporto from `aeroportos` where id_aeroporto=?',array($escala->origem));
                $destino=Aeroporto::find_by_sql('select nome_aeroporto from aeroportos where id_aeroporto=?',array($escala->destino));
            }
        }

        return View::make('voo.gestaoVoos',['voos'=>$voos,'origem'=>$origem,'destino'=>$destino]);
    }

    public function pdfs(){
        $passagensVendas = PassagemVenda::all();

        //$passagensVendas->id_passagem;
        return View::make('passagemvenda.pdfs',['passagensvendas'=>$passagensVendas]);
    }

    public function edit($id){
        $voo = Voo::find([$id]);

        if (is_null($voo)) {
            //TODO redirect to standard error page
        } else {
            return View::make('voo.edit', ['voo' => $voo]);
        }
    }

    public function update($id)
    {
        //find resource (activerecord/model) instance where PK = $id
        //your form name fields must match the ones of the table fields
        $voo = Voo::find([$id]);
        $voo->update_attributes(Post::getAll());

        //if($aeroporto->is_valid()){
            $voo->save();
            Redirect::toRoute('voo/ListarVoos');
        /*} else {
            //redirect to form with data and errors
            Redirect::flashToRoute('aeroporto/edit', ['aeroporto' => $aeroporto]);
        }*/
    }

    public function destroy($id)
    {
        $voo = Voo::find([$id]);
        $voo->delete();
        Redirect::toRoute('voo/ListarVoos');
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