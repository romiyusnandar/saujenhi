import Image from 'next/image';
import Link from 'next/link';
import bg from '@/assets/bg.png';

const Welcome = () => {
  return (
    <div className="relative min-h-screen flex items-center justify-center bg-black overflow-hidden">
      {/* Background Image with Overlay */}
      <div className="absolute inset-0 z-0">
        <Image
          src={bg}
          layout="fill"
          objectFit="cover"
          quality={100}
          alt="Background"
          className="opacity-70"
        />
        <div className="absolute inset-0 bg-gradient-to-b from-black/30 to-black/70"></div>
      </div>

      {/* Content */}
      <div className="z-10 text-center px-4 max-w-4xl mx-auto">
        <div className="mb-16 md:mb-32">
          <h1 className="text-6xl md:text-7xl lg:text-8xl font-extrabold text-white mb-4">
            Annyeong
          </h1>
          <p className="text-3xl md:text-4xl lg:text-5xl font-bold text-pink-400">
            Your Culinary Journey Starts Here!
          </p>
        </div>

        {/* Buttons */}
        <div className="space-y-6 text-center">
          <Link href="/login">
            <p className="inline-block bg-pink-600 text-white py-3 px-8 rounded-full text-lg font-semibold shadow-lg hover:bg-pink-700 transform hover:scale-105 transition duration-300 ease-in-out">
              Masuk
            </p>
          </Link>
          <div className="text-white text-lg">
            Belum Punya Akun?{' '}
            <Link href="/register">
              <p className="text-pink-400 hover:text-pink-300 font-semibold hover:underline inline-block transform hover:scale-105 transition duration-300 ease-in-out">
                Daftar
              </p>
            </Link>
          </div>
        </div>
      </div>

      {/* Decorative Elements */}
      <div className="absolute top-0 left-0 w-64 h-64 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
      <div className="absolute top-0 right-0 w-64 h-64 bg-yellow-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
      <div className="absolute -bottom-8 left-20 w-64 h-64 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>
  );
};

export default Welcome;