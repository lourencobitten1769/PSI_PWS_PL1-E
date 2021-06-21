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




    public function HistoricoPassagem(){

        $id=$_SESSION['id'];
        //echo $id;

       // $passagens=PassagemVenda::find_by_sql('select * from `passagem_vendas` where id_utilizador=?',array($id));
        $passagens = PassagemVenda::find_all_by_id_utilizador($id);
        foreach($passagens as $passagem){
            $origem=Aeroporto::find_by_sql('select nome_aeroporto from `aeroportos` where id_aeroporto=?',array($passagem->origem));
            $destino=Aeroporto::find_by_sql('select nome_aeroporto from aeroportos where id_aeroporto=?',array($passagem->destino));
           // var_dump($origem);
            //var_dump($destino);
        }
        //var_dump($origem);
        //var_dump($passagens);

        return View::make('passagemvenda.historico',['passagens'=>$passagens,'origens'=>$origem,'destinos'=>$destino]);
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

    public function fazerCheckin(){
        $passagens= PassagemVenda::all(array('conditions' => array('checkin = 0')));
        foreach($passagens as $passagem){
            $origem=Aeroporto::find_by_sql('select nome_aeroporto from `aeroportos` where id_aeroporto=?',array($passagem->origem));
            $destino=Aeroporto::find_by_sql('select nome_aeroporto from aeroportos where id_aeroporto=?',array($passagem->destino));
        }
        return View::make('passagemvenda.fazerCheckin',['passagens'=>$passagens,'origens'=>$origem,'destinos'=>$destino]);
    }

    public function confirmarCheckin($id){
        $passagem= PassagemVenda::find([$id]);
        $passagem->update_attribute('checkin', 1);
        $passagem->save();
        Redirect::toRoute('passagemvenda/fazerCheckin');
    }

    public function passagensCompradasNosUltimos15dias(){
        $passagens=PassagemVenda::find_by_sql('SELECT * FROM passagem_vendas WHERE DATEDIFF(NOW(), data_compra)<=15;');
        foreach($passagens as $passagem){
            $origem=Aeroporto::find_by_sql('select nome_aeroporto from `aeroportos` where id_aeroporto=?',array($passagem->origem));
            $destino=Aeroporto::find_by_sql('select nome_aeroporto from aeroportos where id_aeroporto=?',array($passagem->destino));
        }
        return View::make('passagemvenda.detalhesPassagens',['passagens'=>$passagens,'origens'=>$origem,'destinos'=>$destino]);
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