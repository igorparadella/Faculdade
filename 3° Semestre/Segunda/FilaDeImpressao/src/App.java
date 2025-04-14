import java.util.LinkedList;
import java.util.Queue;
import java.util.Scanner;

public class App {
    public static void main(String[] args) {
        Queue<String> fila = new LinkedList<>();
        Scanner sc = new Scanner(System.in);
        int opcao;

        do {
            System.out.println("\n--- Simulador de Fila de Impressão ---");
            System.out.println("1. Adicionar novo documento à fila");
            System.out.println("2. Imprimir próximo documento");
            System.out.println("3. Verificar quantidade de documentos na fila");
            System.out.println("0. Sair");
            System.out.print("Escolha uma opção: ");
            opcao = sc.nextInt();
            sc.nextLine();
            
            switch (opcao) {
                case 1:
                    System.out.print("Informe o nome do documento: ");
                    String documento = sc.nextLine();
                    fila.add(documento);
                    System.out.println("Documento adicionado com sucesso.");
                    break;
                case 2:
                    if (fila.isEmpty()) {
                        System.out.println("Fila vazia! Não há documentos para imprimir.");
                    } else {
                        String documentoImpressao = fila.poll();
                        System.out.println("Imprimindo o documento: " + documentoImpressao);
                    }
                    break;
                case 3:
                    System.out.println("Há " + fila.size() + " documento(s) na fila.");
                    break;
                case 0:
                    System.out.println("Encerrando o programa.");
                    break;
                default:
                    System.out.println("Opção inválida! Tente novamente.");
            }
        } while (opcao != 0);

        sc.close();
    }
}
