<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {
  public function timeline() {
    $this->validaAutenticacao();

    $tweet = Container::getModel("Tweet");
    $tweet->__set("id_usuario", $_SESSION["id"]);
    // $tweets = $tweet->getAll();

    $total_registros_pagina = 10;
    $deslocamento = 0;
    $tweets = $tweet->getPorPagina($total_registros_pagina, $deslocamento);

    $this->view->tweets = $tweets;

    $usuario = Container::getModel("Usuario");
    $usuario->__set("id", $_SESSION["id"]);

    $this->view->info_usuario = $usuario->getInfoUsuario();
    $this->view->total_tweets = $usuario->getTotalTweets();
    $this->view->total_seguindo = $usuario->getTotalSeguindo();
    $this->view->total_seguidores = $usuario->getTotalSeguidores();

    $this->render("timeline");
  }

  public function tweet() {
    $this->validaAutenticacao();

    $tweet = Container::getModel("Tweet");

    $tweet->__set("tweet", $_POST["tweet"]);
    $tweet->__set("id_usuario", $_SESSION["id"]);

    $tweet->salvar();

    header("Location: /timeline");
  }

  public function validaAutenticacao() {
    session_start();

    if (!isset($_SESSION["id"]) || empty($_SESSION["id"]) || !isset($_SESSION["nome"]) || empty($_SESSION["nome"])) {
      header("Location: /?login=erro");
    }
  }

  public function quemSeguir() {
    $this->validaAutenticacao();

    $pesquisarPor = isset($_GET["pesquisarPor"]) ? $_GET["pesquisarPor"] : "";
    $usuarios = array();
    $usuario = Container::getModel("Usuario");

    if ($pesquisarPor != "") {
      $usuario->__set("nome", $pesquisarPor);
      $usuario->__set("id", $_SESSION["id"]);
      $usuarios = $usuario->getAll();
    }
    $usuario->__set("id", $_SESSION["id"]);

    print_r($_SESSION);

    $this->view->info_usuario = $usuario->getInfoUsuario();
    $this->view->total_tweets = $usuario->getTotalTweets();
    $this->view->total_seguindo = $usuario->getTotalSeguindo();
    $this->view->total_seguidores = $usuario->getTotalSeguidores();

    $this->view->usuarios = $usuarios;

    $this->render("quemSeguir");
  }

  public function acao() {
    $this->validaAutenticacao();

    $acao = isset($_GET["acao"]) ? $_GET["acao"] : "";
    $id_usuario_seguindo = isset($_GET["id_usuario"]) ? $_GET["id_usuario"] : "";

    $usuario = Container::getModel("Usuario");
    $usuario->__set("id", $_SESSION["id"]);

    if ($acao == "seguir") {
      $usuario->seguirUsuario($id_usuario_seguindo);
    } else if ($acao == "deixar_de_seguir") {
      $usuario->deixarSeguirUsuario($id_usuario_seguindo);
    }

    header("Location: /quem_seguir");
  }

  public function removeTweet() {
    $this->validaAutenticacao();

    $tweets = Container::getModel("Tweet");
    $tweets->__set("id", $_GET["id"]);

    $tweets->deletaTweet();

    header("Location: /timeline");
  }
}
