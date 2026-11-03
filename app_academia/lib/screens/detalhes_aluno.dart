
import 'package:flutter/material.dart';

import '../models/aluno.dart';

class DetalhesAluno extends StatelessWidget {
  final Aluno aluno;

  const DetalhesAluno({
    super.key,
    required this.aluno,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detalhes do Aluno'),
        centerTitle: true,
      ),

      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),

        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [

            const SizedBox(height: 20),

            // Foto do aluno
            CircleAvatar(
              radius: 65,
              backgroundColor: Colors.green.shade100,

              backgroundImage: aluno.imagemUrl.isNotEmpty
                  ? NetworkImage(aluno.imagemUrl)
                  : null,

              child: aluno.imagemUrl.isEmpty
                  ? const Icon(
                      Icons.person,
                      size: 70,
                      color: Colors.green,
                    )
                  : null,
            ),

            const SizedBox(height: 20),

            // Nome do aluno
            Text(
              aluno.nome,
              style: const TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
              ),
              textAlign: TextAlign.center,
            ),

            const SizedBox(height: 10),

            // Situação do aluno
            Chip(
              label: Text(
                aluno.ativo ? 'Aluno Ativo' : 'Aluno Inativo',
              ),

              backgroundColor: aluno.ativo
                  ? Colors.green.shade100
                  : Colors.red.shade100,
            ),

            const SizedBox(height: 30),

            // Informações pessoais
            const Align(
              alignment: Alignment.centerLeft,
              child: Text(
                'Informações pessoais',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),

            const SizedBox(height: 15),

            Card(
              child: Column(
                children: [

                  ListTile(
                    leading: const Icon(Icons.badge),
                    title: const Text('Código do aluno'),
                    subtitle: Text(aluno.id.toString()),
                  ),

                  const Divider(height: 1),

                  ListTile(
                    leading: const Icon(Icons.person),
                    title: const Text('Nome completo'),
                    subtitle: Text(aluno.nome),
                  ),

                  const Divider(height: 1),

                  ListTile(
                    leading: const Icon(Icons.cake),
                    title: const Text('Idade'),
                    subtitle: Text('${aluno.idade} anos'),
                  ),

                  const Divider(height: 1),

                  ListTile(
                    leading: const Icon(Icons.phone),
                    title: const Text('Telefone'),
                    subtitle: Text(aluno.telefone),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 25),

            // Informações do plano
            const Align(
              alignment: Alignment.centerLeft,
              child: Text(
                'Plano contratado',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),

            const SizedBox(height: 15),

            Card(
              child: ListTile(
                leading: const Icon(
                  Icons.fitness_center,
                  color: Colors.green,
                  size: 35,
                ),

                title: Text(aluno.planoNome),

                subtitle: Text(
                  'Código do plano: ${aluno.planoId}',
                ),
              ),
            ),

            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }
}
