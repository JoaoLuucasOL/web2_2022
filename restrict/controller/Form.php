<?php
class Form
{
  private $message = "";
  private $error = "";
  public function __construct()
  {
    Transaction::open();
  }
  public function controller()
  {
    $form = new Template("restrict/view/form.html");
    $form->set("id", "");
    $form->set("nome", "");
    $form->set("localizacao", "");
    $form->set("atracoes", "");
    $this->message = $form->saida();
  }
  public function salvar()
  {
    if (isset($_POST["nome"]) && isset($_POST["localizacao"]) && isset($_POST["atracoes"])) {
      try {
        $conexao = Transaction::get();
        $parque = new Crud("parque");
        $nome = $conexao->quote($_POST["nome"]);
        $localizacao = $conexao->quote($_POST["localizacao"]);
        $atracoes = $conexao->quote($_POST["atracoes"]);
        if (empty($_POST["id"])) {
          $parque->insert(
            "nome, localizacao, atracoes",
            "$nome, $localizacao, $atracoes"
          );
        } else {
          $id = $conexao->quote($_POST["id"]);
          $parque->update(
            "nome = $nome, localizacao = $localizacao, atracoes = $atracoes",
            "id = $id"
          );
        }
        $this->message = $parque->getMessage();
        $this->error = $parque->getError();
      } catch (Exception $e) {
        $this->message = $e->getMessage();
        $this->error = true;
      }
    } else {
      $this->message = "Campos não informados!";
      $this->error = true;
    }
  }
  public function editar()
  {
    if (isset($_GET["id"])) {
      try {
        $conexao = Transaction::get();
        $id = $conexao->quote($_GET["id"]);
        $parque = new Crud("parque");
        $resultado = $parque->select("*", "id = $id");
        if (!$parque->getError()) {
          $form = new Template("restrict/view/form.html");
          foreach ($resultado[0] as $cod => $atracoes) {
            $form->set($cod, $atracoes);
          }
          $this->message = $form->saida();
        } else {
          $this->message = $parque->getMessage();
          $this->error = true;
        }
      } catch (Exception $e) {
        $this->message = $e->getMessage();
        $this->error = true;
      }
    }
  }
  public function getMessage()
  {
    if (is_string($this->error)) {
      return $this->message;
    } else {
      $msg = new Template("shared/view/msg.html");
      if ($this->error) {
        $msg->set("cor", "danger");
      } else {
        $msg->set("cor", "success");
      }
      $msg->set("msg", $this->message);
      $msg->set("uri", "?class=Tabela");
      return $msg->saida();
    }
  }
  public function __destruct()
  {
    Transaction::close();
  }
}