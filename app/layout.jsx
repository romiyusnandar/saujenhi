import "./globals.css";
import { Poppins } from "next/font/google";

export const metadata = {
  title: "Saujenhi",
  description: "Korean food products",
};

const poppins = Poppins({
  subsets: ["latin"],
  weight: ["400", "500", "600", "700", "800", "900"],
});


export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body
        className={poppins}
      >
        {children}
      </body>
    </html>
  );
}
