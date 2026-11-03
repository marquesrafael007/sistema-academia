<?php
class PlanoController {
    public function listar(): void {
        $planos=(new Plano())->listar();
        require __DIR__.'/../views/planos/index.php';
    }
    public function cadastrar(): void { $this->formulario(); }
    public function editar(): void {
        $id=idValido($_GET['id']??null);
        if (!$id) { erroPagina(400,'ID inválido.'); return; }
        $this->formulario($id);
    }
    private function formulario(?int $id=null): void {
        $model=new Plano();
        $dados=$id?$model->buscar($id):['nome'=>'','valor'=>''];
        if (!$dados) { erroPagina(404,'Registro não encontrado.'); return; }
        $erros=[];
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            [$dados,$erros]=validarPlano($_POST);
            if (!$erros) { $model->salvar($dados,$id); setFlash('success','Registro salvo.'); redirect('plano'); }
            http_response_code(400);
        }
        
        require __DIR__.'/../views/planos/form.php';
    }
    public function excluir(): void {
        $id=idValido($_POST['id']??null);
        if (!$id) { erroPagina(400,'ID inválido.'); return; }
        $model=new Plano();
        if (!$model->buscar($id)) { erroPagina(404,'Registro não encontrado.'); return; }
        try { $model->excluir($id); setFlash('success','Registro excluído.'); }
        catch (PDOException $erro) {
            if (($erro->errorInfo[1]??null)!=1451) { throw $erro; }
            setFlash('danger','Este plano possui alunos. Altere o plano desses alunos antes de excluir.');
        }
        redirect('plano');
    }
}
