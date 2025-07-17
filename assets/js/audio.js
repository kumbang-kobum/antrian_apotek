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
  if (isPlaying) return; // Cegah tumpang tindih audio

  const folder = "../assets/audio/";
  audioQueue = [];

  // Tambahkan awalan "Antrian Nomor"
  audioQueue.push("antrian");
  audioQueue.push("nomor");

  // Tambahkan angka ke queue
  audioQueue.push(...pecahAngka(parseInt(nomor)));

  isPlaying = true;
  playQueue(folder);
}

function pecahAngka(n) {
  const hasil = [];

  if (n === 0) return ['0'];

  if (n >= 1000) {
    const ribu = Math.floor(n / 1000);
    hasil.push(...pecahAngka(ribu));
    hasil.push("1000"); // pastikan file 1000.wav = "ribu"
    n %= 1000;
  }

  if (n >= 100) {
    const ratus = Math.floor(n / 100);
    if (ratus > 1) hasil.push(ratus.toString()); // 2 ratus, 3 ratus
    hasil.push("100"); // 100.wav = "ratus"
    n %= 100;
  }

  if (n >= 11 && n <= 19) {
    hasil.push(n.toString()); // 11.wav - 19.wav
    return hasil;
  }

  if (n === 10) {
    hasil.push("10");
    return hasil;
  }

  if (n >= 20) {
    const puluhan = Math.floor(n / 10) * 10;
    hasil.push(puluhan.toString()); // 20, 30, dst
    if (n % 10 !== 0) hasil.push((n % 10).toString());
    return hasil;
  }

  if (n > 0) {
    hasil.push(n.toString());
  }

  return hasil;
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