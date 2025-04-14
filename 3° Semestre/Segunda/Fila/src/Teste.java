/*
public class Teste {
    public static void main(String[] args) {
        Fila<Integer> fila = new Fila<>();

        fila.enfileira(1);
        fila.enfileira(2);
        fila.enfileira(3);

        Integer primeiro = fila.espiar();

        if (primeiro == null) {
            System.out.println("Ninguém na fila.");
        } else {
            System.out.println("O elemento da primeira posição: " + primeiro);
        }

        System.out.println("A fila está vazia? " + fila.estaVazia());
        System.out.println("O tamanho da fila é de: " + fila.tamanho());
        System.out.println("Conteúdo da fila: " + fila.toString());
    }
}
*/

import java.util.LinkedList;
import java.util.Queue;

public class Teste {
    public static void main(String[] args) {
        Queue<Integer> fila = new LinkedList<>();

        // Enfileirar elementos
        fila.offer(1);
        fila.offer(2);
        fila.offer(3);

        // Espiar (ver o primeiro elemento sem remover)
        Integer primeiro = fila.peek();

        if (primeiro == null) {
            System.out.println("Ninguém na fila.");
        } else {
            System.out.println("O elemento da primeira posição: " + primeiro);
        }

        // Verifica se a fila está vazia
        System.out.println("A fila está vazia? " + fila.isEmpty());

        // Tamanho da fila
        System.out.println("O tamanho da fila é: " + fila.size());

        // Exibir fila
        System.out.println("Conteúdo da fila: " + fila);

        // Desenfileirar (remover o primeiro)
        Integer removido = fila.poll();
        System.out.println("Elemento removido da fila: " + removido);
        System.out.println("Fila após remoção: " + fila);
    }
}
