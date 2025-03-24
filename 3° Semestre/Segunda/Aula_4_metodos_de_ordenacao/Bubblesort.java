public class Bubblesort {
    public static void main(String[] args) {
        int[] vetor = new int[10];

        for(int i = 0; i < vetor.length; i++) {
            vetor[i] = (int) (Math.random() * 10);

            System.out.println(vetor[i]);
        }
        
    }
}
