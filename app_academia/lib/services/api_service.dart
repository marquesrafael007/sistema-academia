
import 'dart:convert';
import 'package:http/http.dart' as http;

import '../models/aluno.dart';
import '../models/plano.dart';

class ApiService {

  // Endereço da API PHP no computador (emulador Android)
  static const String baseUrl =
      'http://10.0.2.2/sistema-academia/api';

  // 1. Listar todos os alunos
  Future<List<Aluno>> listarAlunos() async {

    final resposta = await http.get(
      Uri.parse('$baseUrl/alunos.php'),
    );

    if (resposta.statusCode == 200) {

      final dados = jsonDecode(
        utf8.decode(resposta.bodyBytes),
      );

      return (dados as List)
          .map((item) => Aluno.fromJson(item))
          .toList();

    } else {
      throw Exception('Não foi possível carregar os alunos.');
    }
  }

  // 2. Buscar um aluno pelo ID
  Future<Aluno> buscarAluno(int id) async {

    final resposta = await http.get(
      Uri.parse('$baseUrl/alunos.php?id=$id'),
    );

    if (resposta.statusCode == 200) {

      final dados = jsonDecode(
        utf8.decode(resposta.bodyBytes),
      );

      return Aluno.fromJson(dados);

    } else {
      throw Exception('Não foi possível encontrar o aluno.');
    }
  }

  // 3. Cadastrar um novo aluno
  Future<Aluno> cadastrarAluno({
    required String nome,
    required int idade,
    required String telefone,
    required int planoId,
    String imagemUrl = '',
    bool ativo = true,
  }) async {

    final resposta = await http.post(
      Uri.parse('$baseUrl/alunos.php'),

      headers: {
        'Content-Type': 'application/json',
      },

      body: jsonEncode({
        'nome': nome,
        'idade': idade,
        'ativo': ativo,
        'telefone': telefone,
        'imagem_url': imagemUrl,
        'plano_id': planoId,
      }),
    );

    if (resposta.statusCode == 201) {

      final dados = jsonDecode(
        utf8.decode(resposta.bodyBytes),
      );

      return Aluno.fromJson(dados);

    } else {
      throw Exception('Não foi possível cadastrar o aluno.');
    }
  }

  // 4. Listar os planos disponíveis
  Future<List<Plano>> listarPlanos() async {

    final resposta = await http.get(
      Uri.parse('$baseUrl/planos.php'),
    );

    if (resposta.statusCode == 200) {

      final dados = jsonDecode(
        utf8.decode(resposta.bodyBytes),
      );

      return (dados as List)
          .map((item) => Plano.fromJson(item))
          .toList();

    } else {
      throw Exception('Não foi possível carregar os planos.');
    }
  }
}
