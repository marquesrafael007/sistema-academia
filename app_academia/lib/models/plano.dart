
class Plano {
  final int id;
  final String nome;
  final String valor;

  Plano({
    required this.id,
    required this.nome,
    required this.valor,
  });

  // Converte o JSON da API em um objeto Plano
  factory Plano.fromJson(Map<String, dynamic> json) {
    return Plano(
      id: int.parse(json['id'].toString()),
      nome: json['nome'] ?? '',
      valor: json['valor'].toString(),
    );
  }

  // Converte o objeto Plano em JSON
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nome': nome,
      'valor': valor,
    };
  }
}
