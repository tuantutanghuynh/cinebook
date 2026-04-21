import { Link, useNavigate } from "react-router-dom";
import api from "../services/api";

export default function Header() {
  const navigate = useNavigate();
  const user = JSON.parse(localStorage.getItem("user"));

  const handleLogout = async () => {
    try {
      await api.post("/logout");
    } finally {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      navigate("/login");
    }
  };

  return (
    <header>
      <Link to="/">CineBook</Link>

      <nav>
        <Link to="/">Home</Link>
        <Link to="/now-showing">Now Showing</Link>
        <Link to="/upcoming">Upcoming</Link>
      </nav>

      <div>
        {user ? (
          <>
            <span>Hello, {user.name}</span>
            <button onClick={handleLogout}>Logout</button>
          </>
        ) : (
          <>
            <Link to="/login">Login</Link>
            <Link to="/register">Register</Link>
          </>
        )}
      </div>
    </header>
  );
}
