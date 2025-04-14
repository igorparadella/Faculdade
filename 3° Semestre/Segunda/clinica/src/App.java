import java.util.LinkedList;
import java.util.Queue;
import java.util.Scanner;

public class App {

    public static void main(String[] args) {
        Queue<String> filaPacientes = new LinkedList<>();
        Scanner sc = new Scanner(System.in);
        int opcao;

        do {
            System.out.println("\n--- Sistema de Atendimento da Clínica ---");
            System.out.println("1. Adicionar paciente à fila");
            System.out.println("2. Atender próximo paciente");
            System.out.println("3. Verificar se a fila está vazia");
            System.out.println("0. Sair");
            System.out.print("Escolha uma opção: ");
            opcao = sc.nextInt();
            sc.nextLine(); 

            switch (opcao) {
                case 1:
                    System.out.print("Nome do paciente: ");
                    String nome = sc.nextLine();
                    filaPacientes.add(nome);
                    System.out.println(nome + " foi adicionado à fila.");
                    break;
                case 2:
                    if (!filaPacientes.isEmpty()) {
                        String proximo = filaPacientes.poll();
                        System.out.println("Próximo paciente a ser atendido: " + proximo);
                    } else {
                        System.out.println("A fila está vazia. Nenhum paciente para atender.");
                    }
                    break;
                case 3:
                    if (filaPacientes.isEmpty()) {
                        System.out.println("A fila está vazia.");
                    } else {
                        System.out.println("A fila possui " + filaPacientes.size() + " paciente(s).");
                    }
                    break;
                case 0:
                    System.out.println("Encerrando o sistema...");
                    break;
                default:
                    System.out.println("Opção inválida. Tente novamente.");
            }

        } while (opcao != 0);

        sc.close();
    }
}
