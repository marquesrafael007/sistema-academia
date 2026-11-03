
import 'package:flutter/material.dart';

import '../models/aluno.dart';
import '../services/api_service.dart';
import 'detalhes_aluno.dart';
import 'cadastro_aluno.dart';

class ListaAlunos extends StatefulWidget {
  const ListaAlunos({super.key, this.api});
  final ApiService? api;

  @override
  State<ListaAlunos> createState() => _ListaAlunosState();
}

class _ListaAlunosState extends State<ListaAlunos> {

  late final ApiService api;

  late Future<List<Aluno>> futureAlunos;

  @override
  void initState() {
    super.initState();
    api = widget.api ?? ApiService();

    // Busca os alunos quando a tela é aberta
    futureAlunos = api.listarAlunos();
  }

  // Atualiza a lista de alunos
  Future<void> atualizarLista() async {
    setState(() {
      futureAlunos = api.listarAlunos();
    });

    // O FutureBuilder apresenta o erro; não deixe o gesto de atualizar lançá-lo.
    try { await futureAlunos; } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(

      // Barra superior
      appBar: AppBar(
        title: const Text('Academia - Alunos'),
        centerTitle: true,
      ),

      // Corpo da tela
      body: RefreshIndicator(
        onRefresh: atualizarLista,

        child: FutureBuilder<List<Aluno>>(
          future: futureAlunos,

          builder: (context, snapshot) {

            // Enquanto carrega os dados
            if (snapshot.connectionState == ConnectionState.waiting) {
              return ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                children: const [
                  SizedBox(height: 200),
                  Center(child: CircularProgressIndicator()),
                  SizedBox(height: 20),
                  Center(child: Text('Carregando alunos...')),
                ],
              );
            }

            // Quando ocorre algum erro
            if (snapshot.hasError) {
              return ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                children: [
                  const SizedBox(height: 150),

                  const Icon(
                    Icons.error_outline,
                    size: 60,
                    color: Colors.red,
                  ),

                  const Center(
                    child: Text(
                      'Não foi possível carregar os alunos.',
                    ),
                  ),

                  const SizedBox(height: 10),

                  Center(
                    child: TextButton(
                      onPressed: atualizarLista,
                      child: const Text('Tentar novamente'),
                    ),
                  ),
                ],
              );
            }

            // Recebe a lista de alunos
            final alunos = snapshot.data ?? [];

            // Quando não existem alunos cadastrados
            if (alunos.isEmpty) {
              return ListView(
                physics: const AlwaysScrollableScrollPhysics(),
                children: const [
                  SizedBox(height: 200),
                  Center(
                    child: Text('Nenhum aluno cadastrado.'),
                  ),
                ],
              );
            }

            // Exibe os alunos na tela
            return ListView.builder(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(12),
              itemCount: alunos.length,

              itemBuilder: (context, index) {
                final aluno = alunos[index];

                return Card(
                  margin: const EdgeInsets.only(bottom: 12),

                  child: ListTile(
                    contentPadding: const EdgeInsets.all(12),

                    // Foto do aluno
                    leading: CircleAvatar(
                      radius: 28,
                      backgroundColor: Colors.green.shade100,

                      backgroundImage: aluno.imagemUrl.isNotEmpty
                          ? NetworkImage(aluno.imagemUrl)
                          : null,

                      child: aluno.imagemUrl.isEmpty
                          ? const Icon(Icons.person, size: 30)
                          : null,
                    ),

                    // Nome do aluno
                    title: Text(
                      aluno.nome,
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                      ),
                    ),

                    // Informações do aluno
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('Idade: ${aluno.idade} anos'),
                        Text('Plano: ${aluno.planoNome}'),

                        const SizedBox(height: 5),

                        Text(
                          aluno.ativo ? 'Ativo' : 'Inativo',
                          style: TextStyle(
                            color: aluno.ativo
                                ? Colors.green
                                : Colors.red,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),

                    trailing: const Icon(
                      Icons.arrow_forward_ios,
                      size: 18,
                    ),

                    // Abre a tela de detalhes
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => DetalhesAluno(
                            aluno: aluno,
                          ),
                        ),
                      );
                    },
                  ),
                );
              },
            );
          },
        ),
      ),

      // Botão flutuante para cadastrar alunos
      floatingActionButton: FloatingActionButton(
        onPressed: () async {

          final salvo = await Navigator.push<bool>(
            context,
            MaterialPageRoute(
              builder: (context) => CadastroAluno(api: api),
            ),
          );
          if (!context.mounted || salvo != true) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Aluno cadastrado com sucesso.')),
          );
          await atualizarLista();

        },

        child: const Icon(Icons.add),
      ),
    );
  }
}
