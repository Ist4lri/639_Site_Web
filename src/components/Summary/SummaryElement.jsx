import styled from '@emotion/styled';

export const TextContainer = styled.p`
    z-index: 1; /* Assure que le contenu est au-dessus de l'image de fond */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: justify;
    width: 40%; /* Définir la largeur de la colonne centrale, vous pouvez ajuster ce pourcentage selon vos besoins */
    margin: 0 auto; /* Centrer horizontalement */
`;

export const BlackBackgroundContainer = styled.div`
    position: relative;
    background-color: #000;
    color: #fff;
    padding: 50px 0;
    width: 100%; /* Assurez-vous que l'élément prend toute la largeur disponible */
`;

export const PicturebackgroundContainer = styled.div`
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 100vh; /* Utiliser min-height pour s'assurer que l'image de fond occupe au moins la hauteur de la vue */
    background-image: url('../assets/Drapeau639v21.png');
    background-size: cover;
    background-position: center;
    text-align: center;
`;

export const SummaryContainer = styled.div`
    position: relative;
    min-height: 100vh;
`;