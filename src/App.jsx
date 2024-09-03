import Navbar from "./components/Navbar/";
import {
    BrowserRouter as Router,
    Routes,
    Route,
} from "react-router-dom";
import SummaryPage from './components/Summary/';
import IdentityPage from './components/Identity/';
import StaffPage from './components/Staff/';
import JoinPage from './components/Join/';


function App() {

  return (
    <Router>
      <Navbar />
      <Routes>
          <Route exact path="/" element={<SummaryPage />} />
          <Route path="/Identity" element={<IdentityPage />} />
          <Route path="/Staff" element={<StaffPage />} />
          <Route path="/Join" element={<JoinPage />} />
      </Routes>
  </Router>
  )
}

export default App
