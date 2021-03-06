upvote = (e) => {
    const URL = "http://127.0.0.1:8000/api/vote";
    const id = e.target.parentNode.id
    const data = {"id": id,"vote": "up"}

    if(addToLikes(id)) {
        fetch(URL, {
            method: 'post',
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data)
        })
            .then((response) => {
                const likes = parseInt(document.getElementById(id + "p").innerHTML) + 1
                document.getElementById(id + "p").innerHTML = `${likes} likes!`
                document.getElementById("likeButton").disabled = true
            })
            .catch((e) => {
                console.log(e)
            });
    }
}

addToLikes = (id) => {
    let double = false
    if(window.localStorage.getItem('likes')) {
        let likes = JSON.parse(window.localStorage.getItem('likes'))
        double = likes.includes(id)
        if(!double) {
            likes.push(id)
            window.localStorage.setItem('likes', JSON.stringify(likes))
            return true
        } else {
            return false
        }
    } else {
        let likes = new Array()
        likes.push(id)
        window.localStorage.setItem('likes', JSON.stringify(likes))
        return true
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const likeButton = document.getElementById("likeButton")
    const parentNode = likeButton.parentElement
    const liked = window.localStorage.getItem('likes').includes(parentNode.id)
    liked ? likeButton.disabled = true : likeButton.disabled = false
    likeButton.addEventListener('click',upvote)
})