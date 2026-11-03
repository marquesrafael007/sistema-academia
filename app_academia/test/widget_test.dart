import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:app_academia/services/api_service.dart';
import 'package:app_academia/screens/lista_alunos.dart';

Map<String, dynamic> aluno(String nome) => {
  'id_aluno': 1, 'nome': nome, 'idade': 23, 'ativo': true,
  'telefone': '11999999999', 'imagem_url': '', 'plano_id': 3,
  'plano_nome': 'Semestral',
};

void main() {
  test('Lê id_aluno retornado pela API PHP', () async {
    final client = MockClient((_) async => http.Response(jsonEncode([aluno('Alice')]), 200));
    addTearDown(client.close);
    final alunos = await ApiService(client: client).listarAlunos();
    expect(alunos.single.id, 1);
    expect(alunos.single.ativo, true);
    expect(alunos.single.toJson()['id_aluno'], 1);
  });

  test('Mostra os detalhes da validação do servidor', () async {
    final client = MockClient((_) async => http.Response(jsonEncode({
      'erro': 'Dados inválidos.', 'detalhes': ['Escolha um plano existente.'],
    }), 400, headers: {'content-type': 'application/json; charset=utf-8'}));
    addTearDown(client.close);
    await expectLater(
      ApiService(client: client).cadastrarAluno(nome: 'Teste', idade: 20, telefone: '11999999999', planoId: 99),
      throwsA(predicate((e) => e.toString().contains('Escolha um plano existente.'))),
    );
  });

  testWidgets('Carrega planos, salva aluno e atualiza a lista', (tester) async {
    bool salvo = false;
    int consultas = 0;
    final client = MockClient((request) async {
      if (request.url.path.endsWith('planos.php')) {
        return http.Response(jsonEncode([{'id': 3, 'nome': 'Semestral', 'valor': '499.90'}]), 200);
      }
      if (request.method == 'POST') {
        final body = jsonDecode(request.body);
        expect(body['plano_id'], 3);
        expect(body['nome'], 'Novo aluno');
        expect(body['idade'], 25);
        salvo = true;
        return http.Response(jsonEncode(aluno('Novo aluno')), 201);
      }
      consultas++;
      return http.Response(jsonEncode(salvo ? [aluno('Novo aluno')] : []), 200);
    });
    addTearDown(client.close);
    await tester.pumpWidget(MaterialApp(home: ListaAlunos(api: ApiService(client: client))));
    await tester.pumpAndSettle();
    expect(find.text('Nenhum aluno cadastrado.'), findsOneWidget);
    await tester.tap(find.byType(FloatingActionButton));
    await tester.pumpAndSettle();
    final campos = find.byType(TextFormField);
    await tester.enterText(campos.at(0), 'Novo aluno');
    await tester.enterText(campos.at(1), '25');
    await tester.enterText(campos.at(2), '11999999999');
    final select = find.byType(DropdownButtonFormField<int>);
    await tester.ensureVisible(select);
    await tester.tap(select);
    await tester.pumpAndSettle();
    await tester.tap(find.text('Semestral').last);
    await tester.pumpAndSettle();
    final botao = find.widgetWithText(ElevatedButton, 'Cadastrar Aluno');
    await tester.ensureVisible(botao);
    await tester.tap(botao);
    await tester.pumpAndSettle();
    expect(salvo, true);
    expect(consultas, 2);
    expect(find.text('Novo aluno'), findsOneWidget);
    expect(find.text('Aluno cadastrado com sucesso.'), findsOneWidget);
  });
}
