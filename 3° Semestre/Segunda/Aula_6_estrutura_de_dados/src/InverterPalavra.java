import java.util.Scanner;
import java.util.Stack;

public class InverterPalavra {
    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);
        
        System.out.print("Digite uma palavra para inverter: ");
        String palavra = scanner.nextLine();
        
        String palavraInvertida = inverterPalavra(palavra);
        
        System.out.println("Palavra original: " + palavra);
        System.out.println("Palavra invertida: " + palavraInvertida);
        
        scanner.close();
    }
    
    public static String inverterPalavra(String palavra) {
        Stack<Character> pilha = new Stack<>();
        
        for (int i = 0; i < palavra.length(); i++) {
            pilha.push(palavra.charAt(i));
        }
        
        StringBuilder invertida = new StringBuilder();
        while (!pilha.isEmpty()) {
            invertida.append(pilha.pop());
        }
        
        return invertida.toString();
    }
}