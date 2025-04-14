import java.util.Scanner;

public class App {
    public static void main(String[] args) {
        Scanner sc = new Scanner(System.in);
        FilaAtendimento fila = new FilaAtendimento();
        int opcao;

        do {
            System.out.println("\n--- Sistema de Atendimento ---");
            System.out.println("1. Adicionar pessoa");
            System.out.println("2. Chamar próxima pessoa");
            System.out.println("3. Mostrar filas");
            System.out.println("0. Sair");
            System.out.print("Escolha uma opção: ");
            opcao = sc.nextInt();
            sc.nextLine();

            switch (opcao) {
                case 1:
                    System.out.print("Nome da pessoa: ");
                    String nome = sc.nextLine();
                    System.out.print("É preferencial? (s/n): ");
                    String resp = sc.nextLine();
                    boolean preferencial = resp.equalsIgnoreCase("s");
                    fila.adicionarPessoa(new Pessoa(nome, preferencial));
                    break;
                case 2:
                    Pessoa proximo = fila.chamarProximo();
                    if (proximo != null) {
                        System.out.println("Chamando: " + proximo);
                    } else {
                        System.out.println("Nenhuma pessoa na fila.");
                    }
                    break;
                case 3:
                    fila.mostrarFilas();
                    break;
                case 0:
                    System.out.println("Encerrando o sistema.");
                    break;
                default:
                    System.out.println("Opção inválida!");
            }

        } while (opcao != 0);

        sc.close();
    }
}
