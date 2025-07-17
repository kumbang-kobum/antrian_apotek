// Pembuat Chandra Irawan M.T.I
// Bagi yang ingin menggunakan dan melakukan perubahan atau penambahan
// sangat di perbolehkan, namun aplikasi ini tidak untuk diperjual/belikan
// bagi yang ingin berdonasi secangkir kopi bisa melalui
// BCA 8110400102 A/N Chandra Irawan 
// ingat untuk tidak DIPERJUAL BELIKAN ini bersifat open source
// pengembagan aplikasi ini berdasarkan logic aplikasi delphi yang telah dibuat oleh 
// Emirza Wira M.T.I yang berbentul exe

 let isPlaying = false;
let audioQueue = [];

function mainkanAudio(nomor, jenis) {
  if (isPlaying) return; // Cegah double klik

  const folder = "../assets/audio/";
  audioQueue = [];

  // Tambahkan awalan
  audioQueue.push("antrian");
  audioQueue.push("nomor");

  // Fungsi konversi angka
  function pecahAngka(n) {
    const hasil = [];
    n = parseInt(n);

    if (n === 0) return ['0'];
    if (n >= 1000) {
      const ribu = Math.floor(n / 1000);
      hasil.push(...pecahAngka(ribu));
      hasil.push("1000");
      n = n % 1000;
    }

    if (n >= 100) {
      const ratus = Math.floor(n / 100) * 100;
      hasil.push(ratus.toString());
      n = n % 100;
    }

    if (n > 20 && n < 100) {
      const puluhan = Math.floor(n / 10) * 10;
      hasil.push(puluhan.toString());
      if (n % 10 !== 0) hasil.push((n % 10).toString());
    } else if (n > 0) {
      hasil.push(n.toString());
    }

    return hasil;
  }

  // Tambahkan ke queue
  audioQueue.push(...pecahAngka(nomor));
  isPlaying = true;
  playQueue(folder);
}

function playQueue(folder) {
  if (audioQueue.length === 0) {
    isPlaying = false;
    return;
  }

  const nextFile = audioQueue.shift();
  const audio = new Audio(folder + nextFile + ".wav");

  audio.play();
  audio.onended = () => playQueue(folder);
}