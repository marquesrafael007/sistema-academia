
class Aluno {
  final int id;
  final String nome;
  final int idade;
  final bool ativo;
  final String telefone;
  final String imagemUrl;
  final int planoId;
  final String planoNome;

  Aluno({
    required this.id,
    required this.nome,
    required this.idade,
    required this.ativo,
    required this.telefone,
    required this.imagemUrl,
    required this.planoId,
    required this.planoNome,
  });

  // Transforma o JSON recebido da API em um objeto Aluno
  factory Aluno.fromJson(Map<String, dynamic> json) {
    return Aluno(
      id: int.parse(json['id_aluno'].toString()),
      nome: json['nome'] ?? '',
      idade: int.parse(json['idade'].toString()),
      ativo: json['ativo'] == true ||
          json['ativo'].toString() == '1',
      telefone: json['telefone'] ?? '',
      imagemUrl: json['imagem_url'] ?? '',
      planoId: int.parse(json['plano_id'].toString()),
      planoNome: json['plano_nome'] ?? '',
    );
  }

  // Transforma um objeto Aluno em JSON
  Map<String, dynamic> toJson() {
    return {
      'id_aluno': id,
      'nome': nome,
      'idade': idade,
      'ativo': ativo,
      'telefone': telefone,
      'imagem_url': imagemUrl,
      'plano_id': planoId,
      'plano_nome': planoNome,
    };
  }
}
