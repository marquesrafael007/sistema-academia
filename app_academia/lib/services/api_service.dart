import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import '../models/aluno.dart';
import '../models/plano.dart';

class ApiService {
  // Para celular físico, informe API_BASE_URL com o IP do computador.
  static final String baseUrl = const String.fromEnvironment('API_BASE_URL').isNotEmpty
      ? const String.fromEnvironment('API_BASE_URL')
      : (!kIsWeb && defaultTargetPlatform == TargetPlatform.android
          ? 'http://10.0.2.2/sistema-academia/api'
          : 'http://localhost/sistema-academia/api');

  final http.Client? client;
  ApiService({this.client});

  Future<http.Response> get(String path) => (client == null
      ? http.get(Uri.parse('$baseUrl/$path'))
      : client!.get(Uri.parse('$baseUrl/$path')))
      .timeout(const Duration(seconds: 15));

  dynamic respostaJson(http.Response resposta, int esperado) {
    final dynamic dados;
    try {
      dados = jsonDecode(utf8.decode(resposta.bodyBytes));
    } catch (_) {
      throw Exception('Resposta inválida. Confira o endereço da API e o Apache.');
    }
    if (resposta.statusCode != esperado) {
      final detalhes = dados is Map ? dados['detalhes'] : null;
      throw Exception(detalhes is List
          ? detalhes.join(' ')
          : dados is Map ? dados['erro'] ?? 'Erro na API.' : 'Erro na API.');
    }
    return dados;
  }

  Future<List<Aluno>> listarAlunos() async {
    final dados = respostaJson(await get('alunos.php'), 200) as List;
    return dados.map((item) => Aluno.fromJson(item)).toList();
  }

  Future<Aluno> buscarAluno(int id) async =>
      Aluno.fromJson(respostaJson(await get('alunos.php?id=$id'), 200));

  Future<List<Plano>> listarPlanos() async {
    final dados = respostaJson(await get('planos.php'), 200) as List;
    return dados.map((item) => Plano.fromJson(item)).toList();
  }

  Future<Aluno> cadastrarAluno({
    required String nome, required int idade, required String telefone,
    required int planoId, String imagemUrl = '', bool ativo = true,
  }) async {
    final url = Uri.parse('$baseUrl/alunos.php');
    final headers = {'Content-Type': 'application/json'};
    final body = jsonEncode({
      'nome': nome, 'idade': idade, 'telefone': telefone,
      'plano_id': planoId, 'imagem_url': imagemUrl, 'ativo': ativo,
    });
    final resposta = await (client == null
        ? http.post(url, headers: headers, body: body)
        : client!.post(url, headers: headers, body: body))
        .timeout(const Duration(seconds: 15));
    return Aluno.fromJson(respostaJson(resposta, 201));
  }
}
