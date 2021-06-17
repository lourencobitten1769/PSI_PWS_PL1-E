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
class PassagemVendaController extends BaseController
{

    public function index(){

        $aeroportos = Aeroporto::all();
        return View::make('passagemvenda.index',['aeroportos'=> $aeroportos]);
    }

    public function ListarVoosConsoanteAOrigem(){
        //echo $_SESSION['id'];

        $origem=Post::get('partida');
        echo $origem;
        //$passagens= PassagemVenda::all(array('conditions' => 'id_utilizador = ?', $_SESSION['id']));

        //$cond=array('conditions' => array("id_utilizador = ?", $_SESSION['id']));
        //$passagens=PassagemVenda::all($cond);

        /*$join = 'INNER JOIN escalas e ON(voos.id_escala = e.id_escala)';
        $voos = Voo::all(array('joins' => $join));*/
        $voos=Voo::find_by_sql('select * from `voos` INNER JOIN escalas a ON(voos.id_escala = a.id_escala) INNER JOIN aeroportos b ON(a.origem = b.id_aeroporto) WHERE a.origem=?',array($origem));
        //var_dump($voos);

        //var_dump($passagens);
       return View::make('passagemvenda.comprarPassagem',['voos'=>$voos]);
    }

    public function pdfs(){
        $passagensVendas = PassagemVenda::all();

        //$passagensVendas->id_passagem;
        return View::make('passagemvenda.pdfs',['passagensvendas'=>$passagensVendas]);
    }

    public function edit($id){
        $aeroporto = Aeroporto::find([$id]);

        if (is_null($aeroporto)) {
            //TODO redirect to standard error page
        } else {
            return View::make('aeroporto.edit', ['aeroporto' => $aeroporto]);
        }
    }

    public function store()
    {
        //create new resource (activerecord/model) instance with data from POST
        //your form name fields must match the ones of the table fields
        $passagem = new PassagemVenda(Post::getAll());
        $passagem->data_compra=date('Y-m-d H:i:s');
        var_dump($passagem);
        if($passagem->is_valid()) {
            $passagem->save();
            return View::make('passagemvenda.comprarPassagem');
        }
        else {
            //redirect to form with data and errors
            Redirect::flashToRoute('passagemvenda/ListarVoosConsoanteAOrigem', ['passagem' => $passagem]);
        }

    }

    public function update($id)
    {
        //find resource (activerecord/model) instance where PK = $id
        //your form name fields must match the ones of the table fields
        $aeroporto = Aeroporto::find([$id]);
        $aeroporto->update_attributes(Post::getAll());

        //if($aeroporto->is_valid()){
            $aeroporto->save();
            Redirect::toRoute('aeroporto/index');
        /*} else {
            //redirect to form with data and errors
            Redirect::flashToRoute('aeroporto/edit', ['aeroporto' => $aeroporto]);
        }*/
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