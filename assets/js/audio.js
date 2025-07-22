let isPlaying = false;
let audioQueue = [];
// Pembuat Chandra Irawan M.T.I

function mainkanAudio(nomor, jenis, loket) {
  if (isPlaying) return; // Cegah double klik

  const folder = "../assets/audio/";
  audioQueue = [];

  audioQueue.push("antrian");

  if (loket) {
    audioQueue.push(`loket-${loket}`);
  }

  audioQueue.push("nomor");

  function pecahAngka(n) {
    const hasil = [];
    n = parseInt(n);

    if (isNaN(n)) return ['0'];
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

    if (n >= 11 && n <= 19) {
      hasil.push(n.toString());
      return hasil;
    }

    if (n >= 20 && n < 100) {
      const puluhan = Math.floor(n / 10) * 10;
      hasil.push(puluhan.toString());
      if (n % 10 !== 0) hasil.push((n % 10).toString());
      return hasil;
    }

    if (n > 0) {
      hasil.push(n.toString());
    }

    return hasil;
  }

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
  audio.onerror = () => {
    console.error(`File audio tidak ditemukan: ${folder + nextFile}.wav`);
    playQueue(folder);
  };
}