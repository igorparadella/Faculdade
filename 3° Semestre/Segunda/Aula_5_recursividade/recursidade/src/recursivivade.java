import java.math.BigInteger;
import java.util.Scanner;

public class recursivivade {
    public static BigInteger fatorial(BigInteger num) {
        // Caso base: fatorial de 0 ou 1 é 1
        if (num.compareTo(BigInteger.ONE) <= 0) {
            return BigInteger.ONE;
        } else {
            return fatorial(num.subtract(BigInteger.ONE)).multiply(num);
        }
    }

    public static void main(String[] args) {
        // Instanciando Scanner para capturar a entrada do usuário
        Scanner entrada = new Scanner(System.in);
        System.out.print("Digite o número que você pretende obter o fatorial: 23");

        // Lendo a entrada como BigInteger
        BigInteger numero = entrada.nextBigInteger();

        // Calculando e exibindo o fatorial do número
        System.out.println("O fatorial de " + numero + " é " + fatorial(numero) + ".");
        entrada.close();
    }
}