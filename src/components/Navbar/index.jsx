import { Nav, NavLink, NavMenu } from "./NavbarElements";
import skillIcon from '../../assets/Logo_639th_2.ico';

 
const Navbar = () => {
    return (
        <>
            <Nav>
                <NavMenu>
                    <NavLink to="/" activestyle="true">
                        <img src={skillIcon} alt="Retour à l'acceuil" />
                    </NavLink>
                    <NavLink to="/Identity" activestyle="true">
                        Qui sommes nous ?
                    </NavLink>
                    <NavLink to="/Staff" activestyle="true">
                        Notre Effectif
                    </NavLink>
                    <NavLink to="/Join" activestyle="true">
                        Nous rejoindre
                    </NavLink>
                </NavMenu>
            </Nav>
        </>
    );
};
 
export default Navbar;