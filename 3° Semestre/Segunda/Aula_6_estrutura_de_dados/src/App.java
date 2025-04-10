import java.util.Arrays;
import java.util.Random;

public class App {
    public static void main(String[] args) {
        Random random = new Random();

        // Testando Lista
        MinhaLista<Integer> lista = new MinhaLista<>();
        for (int i = 0; i < 8; i++) {
            lista.adicionar(random.nextInt(100));
        }
        System.out.println("Lista antes de ordenar: " + lista);
        lista.ordenar();
        System.out.println("Lista ordenada: " + lista);

        // Testando Pilha
        MinhaPilha<Integer> pilha = new MinhaPilha<>();
        for (int i = 0; i < 5; i++) {
            pilha.push(random.nextInt(100));
        }
        System.out.println("Topo da pilha: " + pilha.peek());
        System.out.println("Pilha antes de pop: " + pilha);
        pilha.pop();
        System.out.println("Pilha depois de pop: " + pilha);
    }
}

// Classe base genérica
class EstruturaEstatica<T extends Comparable<T>> {
    protected T[] elementos;
    protected int tamanho;

    @SuppressWarnings("unchecked")
    public EstruturaEstatica(int capacidade) {
        this.elementos = (T[]) new Comparable[capacidade];
        this.tamanho = 0;
    }

    public EstruturaEstatica() {
        this(10);
    }

    protected void aumentaCapacidade() {
        if (this.tamanho == this.elementos.length) {
            elementos = Arrays.copyOf(elementos, elementos.length * 2);
        }
    }

    public int getTamanho() {
        return this.tamanho;
    }

    public boolean estaVazia() {
        return this.tamanho == 0;
    }

    @Override
    public String toString() {
        if (tamanho == 0) return "[]";

        StringBuilder sb = new StringBuilder();
        sb.append("[");
        for (int i = 0; i < tamanho - 1; i++) {
            sb.append(elementos[i]).append(", ");
        }
        sb.append(elementos[tamanho - 1]).append("]");
        return sb.toString();
    }
}

// Lista
class MinhaLista<T extends Comparable<T>> extends EstruturaEstatica<T> {

    public MinhaLista() {
        super();
    }

    public boolean adicionar(T elemento) {
        aumentaCapacidade();
        elementos[tamanho++] = elemento;
        return true;
    }

    public T get(int posicao) {
        if (posicao < 0 || posicao >= tamanho) {
            throw new IndexOutOfBoundsException("Posição inválida");
        }
        return elementos[posicao];
    }

    public boolean remover(T elemento) {
        int pos = -1;
        for (int i = 0; i < tamanho; i++) {
            if (elementos[i].equals(elemento)) {
                pos = i;
                break;
            }
        }
        if (pos == -1) return false;

        for (int i = pos; i < tamanho - 1; i++) {
            elementos[i] = elementos[i + 1];
        }
        tamanho--;
        return true;
    }

    public void ordenar() {
        Arrays.sort(elementos, 0, tamanho);
    }
}

// Pilha
class MinhaPilha<T extends Comparable<T>> extends EstruturaEstatica<T> {

    public MinhaPilha() {
        super();
    }

    public void push(T elemento) {
        aumentaCapacidade();
        elementos[tamanho++] = elemento;
    }

    public T pop() {
        if (estaVazia()) {
            throw new IllegalStateException("Pilha vazia");
        }
        return elementos[--tamanho];
    }

    public T peek() {
        if (estaVazia()) {
            throw new IllegalStateException("Pilha vazia");
        }
        return elementos[tamanho - 1];
    }
}
