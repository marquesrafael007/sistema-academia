# Executar com a API local

Inicie Apache e MySQL no XAMPP. O PHP deve estar em `C:\xampp\htdocs\sistema-academia`.

- Emulador Android: `flutter run` usa `http://10.0.2.2/sistema-academia/api`.
- Navegador no computador: `flutter run -d chrome` usa `http://localhost/sistema-academia/api`.
- Celular físico: informe o IP do computador na mesma rede:

```powershell
flutter run --dart-define=API_BASE_URL=http://192.168.1.10/sistema-academia/api
```

Substitua o IP pelo endereço do seu computador. A mesma opção permite configurar outra porta ou servidor. Reinicie o app após alterar essa configuração.

O Android permite internet e HTTP para acessar o XAMPP local. Para publicar o aplicativo, use uma API HTTPS e remova a permissão `usesCleartextTraffic` do manifest.

O cadastro consulta os planos reais, envia JSON à API e atualiza a lista ao salvar. O identificador retornado pelo PHP é `id_aluno`.

Verificações locais: `flutter analyze` e `flutter test`. Os testes usam respostas simuladas e não alteram o banco.
