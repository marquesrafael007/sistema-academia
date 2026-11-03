
import 'package:flutter/material.dart';
import '../models/plano.dart';
import '../services/api_service.dart';

class CadastroAluno extends StatefulWidget {
  const CadastroAluno({super.key, this.api});
  final ApiService? api;

  @override
  State<CadastroAluno> createState() => _CadastroAlunoState();
}

class _CadastroAlunoState extends State<CadastroAluno> {

  // Chave utilizada para validar o formulário
  final _formKey = GlobalKey<FormState>();

  // Controladores dos campos
  final nomeController = TextEditingController();
  final idadeController = TextEditingController();
  final telefoneController = TextEditingController();
  final imagemController = TextEditingController();

  // Guarda o plano selecionado
  int? planoSelecionado;

  late final ApiService api;
  List<Plano> planos = [];
  bool carregando = true;
  bool salvando = false;
  String? erroPlanos;

  @override
  void initState() {
    super.initState();
    api = widget.api ?? ApiService();
    carregarPlanos();
  }

  Future<void> carregarPlanos() async {
    setState(() { carregando = true; erroPlanos = null; });
    try {
      final resultado = await api.listarPlanos();
      if (!mounted) return;
      setState(() { planos = resultado; carregando = false; });
    } catch (erro) {
      if (!mounted) return;
      setState(() { erroPlanos = erro.toString(); carregando = false; });
    }
  }

  // Libera os controladores quando a tela é fechada
  @override
  void dispose() {
    nomeController.dispose();
    idadeController.dispose();
    telefoneController.dispose();
    imagemController.dispose();
    super.dispose();
  }

  // Valida os campos do formulário
  Future<void> cadastrarAluno() async {
    if (salvando || !_formKey.currentState!.validate()) return;
    setState(() { salvando = true; });
    try {
      await api.cadastrarAluno(
        nome: nomeController.text.trim(),
        idade: int.parse(idadeController.text.trim()),
        telefone: telefoneController.text.trim(),
        imagemUrl: imagemController.text.trim(),
        planoId: planoSelecionado!,
      );
      if (!mounted) return;
      Navigator.pop(context, true);
    } catch (erro) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(erro.toString())));
    } finally {
      if (mounted) setState(() { salvando = false; });
    }
  }

  @override
  Widget build(BuildContext context) {

    return Scaffold(

      // Barra superior
      appBar: AppBar(
        title: const Text('Cadastrar Aluno'),
        centerTitle: true,
      ),

      // Permite rolar a tela
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),

        child: Form(
          key: _formKey,

          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,

            children: [

              const Center(
                child: Icon(
                  Icons.person_add_alt_1,
                  size: 70,
                  color: Colors.green,
                ),
              ),

              const SizedBox(height: 15),

              const Center(
                child: Text(
                  'Novo Aluno',
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),

              const SizedBox(height: 30),

              // CAMPO NOME
              const Text(
                'Nome completo',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),

              const SizedBox(height: 8),

              TextFormField(
                controller: nomeController,

                decoration: const InputDecoration(
                  hintText: 'Digite o nome do aluno',
                  prefixIcon: Icon(Icons.person),
                  border: OutlineInputBorder(),
                ),

                validator: (valor) {
                  if (valor == null || valor.trim().isEmpty) {
                    return 'Informe o nome do aluno';
                  }

                  return null;
                },
              ),

              const SizedBox(height: 20),

              // CAMPO IDADE
              const Text(
                'Idade',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),

              const SizedBox(height: 8),

              TextFormField(
                controller: idadeController,
                keyboardType: TextInputType.number,

                decoration: const InputDecoration(
                  hintText: 'Digite a idade',
                  prefixIcon: Icon(Icons.cake),
                  border: OutlineInputBorder(),
                ),

                validator: (valor) {
                  final idade = int.tryParse(valor ?? '');

                  if (idade == null || idade < 1 || idade > 120) {
                    return 'Informe uma idade entre 1 e 120 anos';
                  }

                  return null;
                },
              ),

              const SizedBox(height: 20),

              // CAMPO TELEFONE
              const Text(
                'Telefone',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),

              const SizedBox(height: 8),

              TextFormField(
                controller: telefoneController,
                keyboardType: TextInputType.phone,

                decoration: const InputDecoration(
                  hintText: '(11) 99999-9999',
                  prefixIcon: Icon(Icons.phone),
                  border: OutlineInputBorder(),
                ),

                validator: (valor) {
                  if (valor == null || valor.trim().isEmpty) {
                    return 'Informe o telefone';
                  }

                  return null;
                },
              ),

              const SizedBox(height: 20),

              // SELEÇÃO DO PLANO
              const Text(
                'Plano da academia',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),

              const SizedBox(height: 8),

              DropdownButtonFormField<int>(

                // initialValue substitui o antigo value
                initialValue: planoSelecionado,

                decoration: const InputDecoration(
                  prefixIcon: Icon(Icons.fitness_center),
                  border: OutlineInputBorder(),
                ),

                hint: const Text('Selecione um plano'),

                items: planos.map((plano) {

                  return DropdownMenuItem<int>(
                    value: plano.id,
                    child: Text(plano.nome),
                  );

                }).toList(),

                onChanged: carregando || salvando ? null : (valor) {
                  setState(() {
                    planoSelecionado = valor;
                  });
                },

                validator: (valor) {
                  if (valor == null) {
                    return 'Selecione um plano';
                  }

                  return null;
                },
              ),

              const SizedBox(height: 20),

              // CAMPO IMAGEM
              if (carregando) const LinearProgressIndicator(),
              if (erroPlanos != null) ...[
                Text(erroPlanos!),
                TextButton(onPressed: carregarPlanos, child: const Text('Tentar carregar planos novamente')),
              ],
              if (!carregando && erroPlanos == null && planos.isEmpty)
                const Text('Cadastre um plano no sistema web antes de continuar.'),
              const Text(
                'URL da imagem (opcional)',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),

              const SizedBox(height: 8),

              TextFormField(
                controller: imagemController,

                decoration: const InputDecoration(
                  hintText: 'https://exemplo.com/foto.jpg',
                  prefixIcon: Icon(Icons.image),
                  border: OutlineInputBorder(),
                ),
              ),

              const SizedBox(height: 30),

              // BOTÃO CADASTRAR
              SizedBox(
                width: double.infinity,
                height: 50,

                child: ElevatedButton.icon(
                  onPressed: salvando || carregando || planos.isEmpty ? null : cadastrarAluno,

                  icon: const Icon(Icons.save),

                  label: Text(
                    salvando ? 'Salvando...' : 'Cadastrar Aluno',
                    style: const TextStyle(fontSize: 16),
                  ),
                ),
              ),

              const SizedBox(height: 15),

            ],
          ),
        ),
      ),
    );
  }
}
