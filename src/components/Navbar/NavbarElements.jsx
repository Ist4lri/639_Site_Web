import { FaBars } from "react-icons/fa";
import { NavLink as Link } from "react-router-dom";
import styled from '@emotion/styled';
 

export const Nav = styled.nav`
	background: linear-gradient(135deg, #1E3A8A, #3B82F6); /* Gradient plus moderne */
	height: 70px;
	display: flex;
	padding: 0.5rem;
	text-shadow: 2px 2px 5px rgba(0, 0, 0, 1);
	z-index: 12;
`;

export const NavLink = styled(Link)`
	color: #F5F5F5;
	display: flex;
	align-items: center;
	text-decoration: none;
	padding: 0 1.5rem;
	height: 100%;
	cursor: pointer;
	font-weight: 600;
	transition: color 0.3s ease-in-out, transform 0.3s ease;

	img {
 	margin-right: 8px; /* Espacement entre l'image et le texte */
 	width: 65px; /* Ajustez la taille de l'icône */
 	height: 65px; /* Ajustez la taille de l'icône */
 	}

	&:hover {
		color: #F97316; 
		transform: translateY(-2px); /* Lien légèrement soulevé au survol */
	}

	&.active {
		color: #FB923C;
		border-bottom: 2px solid #FB923C; /* Soulignement doré pour le lien actif */
	}
`;

export const Bars = styled(FaBars)`
	display: none;
	color: #ffffff;
	@media screen and (max-width: 768px) {
		display: block;
		position: absolute;
		top: 50%;
		right: 1rem;
		transform: translateY(-50%);
		font-size: 2rem;
		cursor: pointer;
		transition: color 0.3s ease-in-out;
		
		&:hover {
			color: #f4d03f; /* Icône dorée au survol */
		}
	}
`;

export const NavMenu = styled.div`
	display: flex;
	align-items: center;
	margin-right: -24px;

	@media screen and (max-width: 768px) {
		display: none;
	}
`;
