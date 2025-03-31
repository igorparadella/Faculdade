public class QuickSort {

    // Método de partição
    static int particao(int[] vetor, int esquerda, int direita) {
        int pivo = vetor[direita];  // Considera o último elemento como pivô
        int i = (esquerda - 1);  // Índice do menor elemento

        for (int j = esquerda; j < direita; j++) {
            if (vetor[j] <= pivo) {
                i++;
                // Troca vetor[i] com vetor[j]
                int temp = vetor[i];
                vetor[i] = vetor[j];
                vetor[j] = temp;
            }
        }
        // Troca vetor[i + 1] com vetor[direita] (pivô)
        int temp = vetor[i + 1];
        vetor[i + 1] = vetor[direita];
        vetor[direita] = temp;

        return i + 1;  // Retorna o índice do pivô
    }

    // Método QuickSort
    static void ordenaQuickSort(int[] vetor, int esquerda, int direita) {
        if (esquerda < direita) {
            int p = particao(vetor, esquerda, direita);
            // Recursão para ordenar a parte esquerda do pivô
            ordenaQuickSort(vetor, esquerda, p - 1);
            // Recursão para ordenar a parte direita do pivô
            ordenaQuickSort(vetor, p + 1, direita);
        }
    }

    public static void main(String[] args) {
        int[] vetor = new int[10];
        
        // Inicializa o vetor com números aleatórios
        for (int i = 0; i < vetor.length; i++) {
            vetor[i] = (int) (Math.random() * vetor.length);
            System.out.println(vetor[i]);
        }

        // Ordena o vetor usando QuickSort
        ordenaQuickSort(vetor, 0, vetor.length - 1);

        // Exibe o vetor ordenado
        System.out.println("\nVetor Ordenado:");
        for (int i = 0; i < vetor.length; i++) {
            System.out.println(vetor[i]);
        }
    }
}
