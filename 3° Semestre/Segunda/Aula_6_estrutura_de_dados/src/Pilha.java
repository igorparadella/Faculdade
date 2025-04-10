class PilhaTeste {
    public static void main(String[] args) {
        Pilha pilha = new Pilha(5);

        pilha.empilhar(10);
        pilha.empilhar(20);
        pilha.empilhar(30);
        pilha.exibir();

        System.out.println("Topo: " + pilha.topo());

        int removido = pilha.desempilhar();
        System.out.println("Removido: " + removido);

        pilha.exibir();
    }
}

public class Pilha {
    private int[] elementos;
    private int topo;
    private int capacidade;

    public Pilha(int capacidade) {
        this.capacidade = capacidade;
        elementos = new int[capacidade];
        topo = -1;
    }

    public void empilhar(int valor) {
        if (topo == capacidade - 1) {
            System.out.println("Pilha cheia!");
        } else {
            elementos[++topo] = valor;
        }
    }

    public int desempilhar() {
        if (estaVazia()) {
            System.out.println("Pilha vazia!");
            return -1;
        }
        return elementos[topo--];
    }

    public int topo() {
        if (estaVazia()) {
            System.out.println("Pilha vazia!");
            return -1;
        }
        return elementos[topo];
    }

    public boolean estaVazia() {
        return topo == -1;
    }

    public int tamanho() {
        return topo + 1;
    }

    public void exibir() {
        if (estaVazia()) {
            System.out.println("Pilha vazia.");
        } else {
            System.out.print("Pilha: ");
            for (int i = 0; i <= topo; i++) {
                System.out.print(elementos[i] + " ");
            }
            System.out.println();
        }
    }
}

